<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\Integration;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $currentStep = 1;
    public $steps = [
        'Integrate',
        'Details',
        'Store', 
        'Fields',
        'Specifications',
        'Synchronisations'
    ];

    public $integrationName = '';
    public $description = '';
    public $selectedStore = '';
    public $storeDetails = [
        'store_name' => '',
        'store_type' => '',
        'store_url' => '',
        'store_description' => ''
    ];
    public $uniqueIdentifier = '';
    public $identificationType = 'SKU-1';
    public $condition = '';
    public $conditionValue = '';
    public $metaTitle = '';
    public $metaDescription = '';
    public $keywords = '';
    
    // Properties for dynamic specifications
    public $specifications = [];
    public $loadingSpecifications = false;
    
    // Properties for dynamic stores
    public $stores = [];
    public $loadingStores = false;
    
    // New properties for API keys step
    public $katanaPimUrl = '';
    public $katanaPimApiKey = '';
    public $webshopUrl = '';
    public $wooCommerceApiKey = '';
    public $wooCommerceApiSecret = '';
    
    // Properties for internal fields - using individual properties for better performance
    public $fieldName = '';
    public $fieldGtin = '';
    public $fieldShortDescription = '';
    public $fieldLongDescription = '';
    public $fieldTaxCategory = '';
    
    // Properties for select value dropdowns
    public $selectValue1 = '';
    public $selectValue2 = '';
    public $selectValue3 = '';
    public $selectValue4 = '';
    public $selectValue5 = '';
    public $selectValue6 = '';
    public $selectValue7 = '';
    public $selectValue8 = '';
    public $selectValue9 = '';
    public $selectValue10 = '';
    public $selectValue11 = '';
    public $selectValue12 = '';
    public $selectValue13 = '';
    public $selectValue14 = '';

    public function mount()
    {
        // Load any existing integration data from session if editing
        if (session()->has('integration_draft')) {
            $draft = session('integration_draft');
            foreach ($draft as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }
    }

    public function updated($propertyName)
    {
        // Save current progress to session
        $this->saveProgress();
    }

    protected function saveProgress()
    {
        $progress = [
            'currentStep' => $this->currentStep,
            'integrationName' => $this->integrationName,
            'description' => $this->description,
            'selectedStore' => $this->selectedStore,
            'storeDetails' => $this->storeDetails,
            'uniqueIdentifier' => $this->uniqueIdentifier,
            'identificationType' => $this->identificationType,
            'condition' => $this->condition,
            'conditionValue' => $this->conditionValue,
            'metaTitle' => $this->metaTitle,
            'metaDescription' => $this->metaDescription,
            'keywords' => $this->keywords,
            'katanaPimUrl' => $this->katanaPimUrl,
            'katanaPimApiKey' => $this->katanaPimApiKey,
            'webshopUrl' => $this->webshopUrl,
            'wooCommerceApiKey' => $this->wooCommerceApiKey,
            'wooCommerceApiSecret' => $this->wooCommerceApiSecret,
            'fieldName' => $this->fieldName,
            'fieldGtin' => $this->fieldGtin,
            'fieldShortDescription' => $this->fieldShortDescription,
            'fieldLongDescription' => $this->fieldLongDescription,
            'fieldTaxCategory' => $this->fieldTaxCategory,
            'selectValue1' => $this->selectValue1,
            'selectValue2' => $this->selectValue2,
            'selectValue3' => $this->selectValue3,
            'selectValue4' => $this->selectValue4,
            'selectValue5' => $this->selectValue5,
            'selectValue6' => $this->selectValue6,
            'selectValue7' => $this->selectValue7,
            'selectValue8' => $this->selectValue8,
            'selectValue9' => $this->selectValue9,
            'selectValue10' => $this->selectValue10,
            'selectValue11' => $this->selectValue11,
            'selectValue12' => $this->selectValue12,
            'selectValue13' => $this->selectValue13,
            'selectValue14' => $this->selectValue14,
            'specifications' => $this->specifications,
            'stores' => $this->stores,
        ];
        
        session(['integration_draft' => $progress]);
    }

    public function clearProgress()
    {
        session()->forget('integration_draft');
        $this->reset();
        $this->currentStep = 1;
        $this->storeDetails = [
            'store_name' => '',
            'store_type' => '',
            'store_url' => '',
            'store_description' => ''
        ];
    }

    public function nextStep()
    {
        if ($this->validateStep($this->currentStep)) {
            if ($this->currentStep < count($this->steps)) {
                $this->currentStep++;
            }
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep($step)
    {
        if ($step >= 1 && $step <= count($this->steps)) {
            $this->currentStep = $step;
        }
    }

    protected function validateStep($step)
    {
        switch ($step) {
            case 1:
                $this->validate([
                    'integrationName' => 'required|min:3',
                    'description' => 'nullable|max:500',
                ], [
                    'integrationName.required' => 'Integration name is required.',
                    'integrationName.min' => 'Integration name must be at least 3 characters.',
                    'description.max' => 'Description cannot exceed 500 characters.',
                ]);
                break;
                
            case 2:
                $rules = ['selectedStore' => 'required'];
                $messages = ['selectedStore.required' => 'Please select a store.'];
                
                if ($this->selectedStore === 'new_store') {
                    $rules['storeDetails.store_name'] = 'required|min:2';
                    $messages['storeDetails.store_name.required'] = 'Store name is required for custom stores.';
                    $messages['storeDetails.store_name.min'] = 'Store name must be at least 2 characters.';
                }
                
                $this->validate($rules, $messages);
                break;
                
            case 3:
                $this->validate([
                    'katanaPimUrl' => 'required|url',
                    'katanaPimApiKey' => 'required|min:3',
                    'webshopUrl' => 'required|url',
                    'wooCommerceApiKey' => 'required|min:3',
                    'wooCommerceApiSecret' => 'required|min:3',
                ], [
                    'katanaPimUrl.required' => 'KatanaPIM URL is required.',
                    'katanaPimUrl.url' => 'Please enter a valid URL for KatanaPIM.',
                    'katanaPimApiKey.required' => 'KatanaPIM API key is required.',
                    'katanaPimApiKey.min' => 'API key must be at least 3 characters.',
                    'webshopUrl.required' => 'Webshop URL is required.',
                    'webshopUrl.url' => 'Please enter a valid URL for your webshop.',
                    'wooCommerceApiKey.required' => 'WooCommerce API key is required.',
                    'wooCommerceApiKey.min' => 'API key must be at least 3 characters.',
                    'wooCommerceApiSecret.required' => 'WooCommerce API secret is required.',
                    'wooCommerceApiSecret.min' => 'API secret must be at least 3 characters.',
                ]);
                break;
                
            case 4:
                $this->validate([
                    'selectedStore' => 'required',
                ], [
                    'selectedStore.required' => 'Please select a store.',
                ]);
                break;
                
            case 5:
                // Step 5 validation for Specifications (Product Condition and SEO)
                $this->validate([
                    'condition' => 'required',
                    'conditionValue' => 'required_if:condition,other',
                    'metaTitle' => 'nullable|max:60',
                    'metaDescription' => 'nullable|max:160',
                    'keywords' => 'nullable|max:200',
                ], [
                    'condition.required' => 'Please select a product condition.',
                    'conditionValue.required_if' => 'Please specify the condition value.',
                    'metaTitle.max' => 'Meta title cannot exceed 60 characters.',
                    'metaDescription.max' => 'Meta description cannot exceed 160 characters.',
                    'keywords.max' => 'Keywords cannot exceed 200 characters.',
                ]);
                break;
                
            case 6:
                // Step 7 validation is optional as these are final specification fields
                break;
        }
        
        return true;
    }

    public function saveIntegration()
    {
        // Final validation before saving
        $this->validate([
            'katanaPimUrl' => 'required|url',
            'katanaPimApiKey' => 'required|min:3',
            'webshopUrl' => 'required|url',
            'wooCommerceApiKey' => 'required|min:3',
            'wooCommerceApiSecret' => 'required|min:3',
            'selectedStore' => 'required',
            'integrationName' => 'required|min:3',
        ]);

        try {
            $integration = Integration::create([
                'user_id' => Auth::id(),
                'status' => 'active',
                'integration_name' => $this->integrationName,
                'description' => $this->description,
                'selected_store' => $this->selectedStore,
                'store_details' => $this->storeDetails,
                'unique_identifier' => $this->uniqueIdentifier,
                'identification_type' => $this->identificationType,
                'condition' => $this->condition,
                'condition_value' => $this->conditionValue,
                'meta_title' => $this->metaTitle,
                'meta_description' => $this->metaDescription,
                'keywords' => $this->keywords,
                'katana_pim_url' => $this->katanaPimUrl,
                'katana_pim_api_key' => $this->katanaPimApiKey,
                'webshop_url' => $this->webshopUrl,
                'woo_commerce_api_key' => $this->wooCommerceApiKey,
                'woo_commerce_api_secret' => $this->wooCommerceApiSecret,
                'store_mapping' => $this->selectedStore,
                'field_name' => $this->fieldName,
                'field_gtin' => $this->fieldGtin,
                'field_short_description' => $this->fieldShortDescription,
                'field_long_description' => $this->fieldLongDescription,
                'field_tax_category' => $this->fieldTaxCategory,
                'select_value_1' => $this->selectValue1,
                'select_value_2' => $this->selectValue2,
                'select_value_3' => $this->selectValue3,
                'select_value_4' => $this->selectValue4,
                'select_value_5' => $this->selectValue5,
                'select_value_6' => $this->selectValue6,
                'select_value_7' => $this->selectValue7,
                'select_value_8' => $this->selectValue8,
                'select_value_9' => $this->selectValue9,
                'select_value_10' => $this->selectValue10,
                'select_value_11' => $this->selectValue11,
                'select_value_12' => $this->selectValue12,
                'select_value_13' => $this->selectValue13,
                'select_value_14' => $this->selectValue14,
            ]);

            // Clear the draft session after successful save
            session()->forget('integration_draft');
            
            // Redirect to success page or show success message
            session()->flash('message', 'Integration saved successfully!');
            
            // You can redirect to a dashboard or integrations list
            return redirect()->route('integrations.index');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to save integration: ' . $e->getMessage());
        }
    }

    public function getStepCompletionStatus($step)
    {
        switch ($step) {
            case 1:
                $fields = ['integrationName'];
                $filled = array_filter(array_map(fn($field) => !empty($this->$field), $fields));
                return count($filled) / count($fields) * 100;
                
            case 2:
                $fields = ['selectedStore'];
                $filled = array_filter(array_map(fn($field) => !empty($this->$field), $fields));
                $completion = count($filled) / count($fields) * 100;
                
                // If custom store is selected, also check store details
                if ($this->selectedStore === 'new_store' && !empty($this->selectedStore)) {
                    $storeFields = ['storeDetails.store_name'];
                    $storeFilled = array_filter(array_map(fn($field) => !empty(data_get($this, $field)), $storeFields));
                    $storeCompletion = count($storeFilled) / count($storeFields) * 100;
                    $completion = ($completion + $storeCompletion) / 2;
                }
                
                return $completion;
                
            case 3:
                $fields = ['katanaPimUrl', 'katanaPimApiKey', 'webshopUrl', 'wooCommerceApiKey', 'wooCommerceApiSecret'];
                $filled = array_filter(array_map(fn($field) => !empty($this->$field), $fields));
                return count($filled) / count($fields) * 100;
                
            case 4:
                $fields = ['selectedStore'];
                $filled = array_filter(array_map(fn($field) => !empty($this->$field), $fields));
                return count($filled) / count($fields) * 100;
                
            case 5:
                // Step 5 completion for Specifications (Product Condition and SEO)
                $fields = ['condition'];
                $filled = array_filter(array_map(fn($field) => !empty($this->$field), $fields));
                return count($filled) / count($fields) * 100;
                
            case 6:
                // Step 6 is optional, so always show as complete if user reaches it
                return 100;
                
            default:
                return 0;
        }
    }

    public function isStepComplete($step)
    {
        return $this->getStepCompletionStatus($step) >= 100;
    }
    
    /**
     * Fetch specifications from KatanaPIM API
     */
    public function fetchSpecifications()
    {
        $this->loadingSpecifications = true;
        
        try {
            // Use the provided API endpoint and key
            $url = 'https://leenweb.katanapim.com/api/v1/Specifications';
            $apiKey = 'c26T7NYlHUF9!c3oYErWN6Ehyhe&EGM0uVyEM?UB';
            
            $http = \Illuminate\Support\Facades\Http::withHeaders([
                'ApiKey' => $apiKey,
                'Accept' => 'application/json',
            ])->timeout(30);
            
            // Disable SSL verification for development environments
            if (app()->environment('local', 'development')) {
                $http = $http->withoutVerifying();
            }
            
            $response = $http->get($url);
            
            if ($response->successful()) {
                $data = $response->json();
                $this->specifications = $data['Items'] ?? [];
                $this->dispatch('specifications-loaded');
            } else {
                $this->addError('specifications', 'Failed to load specifications: ' . $response->status());
            }
        } catch (\Exception $e) {
            $this->addError('specifications', 'Error loading specifications: ' . $e->getMessage());
        } finally {
            $this->loadingSpecifications = false;
        }
    }
    
    /**
     * Load specifications when step 5 is reached
     */
    public function loadSpecificationsIfNeeded()
    {
        if ($this->currentStep === 5 && empty($this->specifications)) {
            $this->fetchSpecifications();
        }
    }
    
    /**
     * Fetch stores from KatanaPIM API
     */
    public function fetchStores()
    {
        $this->loadingStores = true;
        
        try {
            // Use the provided API endpoint and key
            $url = 'https://leenweb.katanapim.com/api/v1/Store/GetAll';
            $apiKey = 'c26T7NYlHUF9!c3oYErWN6Ehyhe&EGM0uVyEM?UB';
            
            $http = \Illuminate\Support\Facades\Http::withHeaders([
                'ApiKey' => $apiKey,
                'Accept' => 'application/json',
            ])->timeout(30);
            
            // Disable SSL verification for development environments
            if (app()->environment('local', 'development')) {
                $http = $http->withoutVerifying();
            }
            
            $response = $http->get($url);
            
            if ($response->successful()) {
                $data = $response->json();
                $this->stores = is_array($data) ? $data : [];
                $this->dispatch('stores-loaded');
            } else {
                $this->addError('stores', 'Failed to load stores: ' . $response->status());
            }
        } catch (\Exception $e) {
            $this->addError('stores', 'Error loading stores: ' . $e->getMessage());
        } finally {
            $this->loadingStores = false;
        }
    }
    
    /**
     * Load stores when step 2 is reached
     */
    public function loadStoresIfNeeded()
    {
        if ($this->currentStep === 2 && empty($this->stores)) {
            $this->fetchStores();
        }
    }
    
    public function selectStore($storeType)
    {
        $this->selectedStore = $storeType;
        
        if ($storeType === 'store1') {
            $this->storeDetails = [
                'store_name' => 'Store 1',
                'store_type' => 'main',
                'store_url' => '',
                'store_description' => 'Main Online Store'
            ];
        } elseif ($storeType === 'store2') {
            $this->storeDetails = [
                'store_name' => 'Store 2',
                'store_type' => 'secondary',
                'store_url' => '',
                'store_description' => 'Secondary Store'
            ];
        } elseif ($storeType === 'new_store') {
            $this->storeDetails = [
                'store_name' => '',
                'store_type' => '',
                'store_url' => '',
                'store_description' => ''
            ];
        } elseif (str_starts_with($storeType, 'store_')) {
            // Handle dynamic stores from API
            $storeId = str_replace('store_', '', $storeType);
            $selectedStore = collect($this->stores)->firstWhere('Id', $storeId);
            
            if ($selectedStore) {
                $this->storeDetails = [
                    'store_name' => $selectedStore['Name'],
                    'store_type' => 'api_store',
                    'store_url' => '',
                    'store_description' => $selectedStore['SystemName'],
                    'store_id' => $selectedStore['Id']
                ];
            }
        }
        
        $this->saveProgress();
    }
}; ?>

<div style="min-height: 100vh; background-color: #f3f4f6; display: flex;">
    <div style="flex: 1; padding: 2rem;">
        <div style="background-color: white; border-radius: 0.5rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); padding: 2rem;">
            <!-- Stepper -->
            <x-stepper :current-step="$currentStep" :steps="$steps" />

            <!-- Flash Messages -->
            @if (session()->has('message'))
                <div style="
                    background-color: #d1fae5; 
                    border: 1px solid #10b981; 
                    color: #065f46; 
                    padding: 1rem; 
                    border-radius: 0.375rem; 
                    margin-bottom: 1rem;
                ">
                    {{ session('message') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div style="
                    background-color: #fee2e2; 
                    border: 1px solid #ef4444; 
                    color: #991b1b; 
                    padding: 1rem; 
                    border-radius: 0.375rem; 
                    margin-bottom: 1rem;
                ">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Step Content -->
            <div style="min-height: 400px;">
                @if($currentStep === 1)
                    <!-- Step 1: Template -->
                    <div style="display: flex; flex-direction: column; gap: 2rem;">
                        <!-- Template Selection -->
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                            @for($i = 1; $i <= 1; $i++)
                                <div style="
                                    border: 1px solid #e5e7eb; 
                                    border-radius: 0.5rem; 
                                    padding: 1.5rem; 
                                    text-align: center;
                                ">
                                    <div style="
                                        width: 4rem; 
                                        height: 4rem; 
                                        background-color: #dbeafe; 
                                        border-radius: 50%; 
                                        display: flex; 
                                        align-items: center; 
                                        justify-content: center; 
                                        margin: 0 auto 1rem;
                                    ">
                                        <svg style="width: 2rem; height: 2rem; color: #3b82f6;" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                        </svg>
                                    </div>
                                    <button style="
                                        background-color: #9333ea; 
                                        color: white; 
                                        padding: 0.5rem 1rem; 
                                        border-radius: 0.375rem; 
                                        border: none; 
                                        font-weight: 500; 
                                        margin-bottom: 0.5rem; 
                                        cursor: pointer;
                                    ">
                                        WOO
                                    </button>
                                    <p style="color: #6b7280; font-size: 0.875rem;">Connect to your store</p>
                                </div>
                            @endfor
                        </div>

                        <!-- Integration Details Form -->
                        <div style="
                            background-color: white; 
                            border: 1px solid #e5e7eb; 
                            border-radius: 0.5rem; 
                            padding: 1.5rem;
                        ">
                            <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 1.5rem;">Integration Details</h3>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                        Integration Name *
                                    </label>
                                    <input wire:model="integrationName" type="text" placeholder="Enter integration name" style="
                                        width: 100%; 
                                        padding: 0.75rem; 
                                        border: 1px solid #d1d5db; 
                                        border-radius: 0.375rem; 
                                        font-size: 0.875rem;
                                        background-color: white;
                                    ">
                                    @error('integrationName') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                        Description
                                    </label>
                                    <textarea wire:model="description" placeholder="Enter description (optional)" style="
                                        width: 100%; 
                                        padding: 0.75rem; 
                                        border: 1px solid #d1d5db; 
                                        border-radius: 0.375rem; 
                                        font-size: 0.875rem;
                                        background-color: white;
                                        resize: vertical;
                                        min-height: 2.5rem;
                                    "></textarea>
                                    @error('description') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                @elseif($currentStep === 2)
                    <!-- Step 2: Store Selection -->
                    <div style="display: flex; flex-direction: column; gap: 2rem;" wire:init="loadStoresIfNeeded">
                        <div>
                            <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Store Selection</h3>
                            <p style="color: #6b7280; margin-bottom: 2rem;">Choose the store you want to integrate with KatanaPIM.</p>
                            
                            <!-- Load Stores Button -->
                            <div style="margin-bottom: 1.5rem;">
                                <button type="button" wire:click="fetchStores" wire:loading.attr="disabled" style="
                                    padding: 0.5rem 1rem;
                                    background-color: #3b82f6;
                                    color: white;
                                    border: none;
                                    border-radius: 0.375rem;
                                    font-size: 0.875rem;
                                    cursor: pointer;
                                    margin-bottom: 1rem;
                                " wire:loading.class="opacity-50">
                                    <span wire:loading.remove>Load Stores from API</span>
                                    <span wire:loading>Loading...</span>
                                </button>
                                @error('stores') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                            </div>
                            
                            @if($selectedStore)
                                <div style="
                                    background-color: #f0f9ff; 
                                    border: 1px solid #0ea5e9; 
                                    border-radius: 0.5rem; 
                                    padding: 1rem; 
                                    margin-bottom: 1.5rem;
                                ">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <svg style="width: 1.25rem; height: 1.25rem; color: #0ea5e9;" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                        <span style="color: #0c4a6e; font-weight: 500;">
                                            @if($selectedStore === 'new_store')
                                                Custom store configuration
                                            @else
                                                Store selected: {{ $selectedStore }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endif
                            
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                                @if(!empty($stores))
                                    @foreach($stores as $store)
                                        <div style="
                                            border: 2px solid #e5e7eb; 
                                            border-radius: 0.5rem; 
                                            padding: 1.5rem; 
                                            cursor: pointer;
                                            transition: all 0.2s;
                                            background-color: white;
                                        " @if($selectedStore === 'store_' . $store['Id']) style="border-color: #9333ea; background-color: #faf5ff;" @endif wire:click="selectStore('store_{{ $store['Id'] }}')">
                                            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                                                <div style="
                                                    width: 3rem; 
                                                    height: 3rem; 
                                                    background-color: #dbeafe; 
                                                    border-radius: 50%; 
                                                    display: flex; 
                                                    align-items: center; 
                                                    justify-content: center;
                                                ">
                                                    <svg style="width: 1.5rem; height: 1.5rem; color: #3b82f6;" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h4 style="font-weight: 600; color: #111827; margin: 0;">{{ $store['Name'] }}</h4>
                                                    <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">{{ $store['SystemName'] }}</p>
                                                </div>
                                            </div>
                                            <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Store ID: {{ $store['Id'] }}</p>
                                        </div>
                                    @endforeach
                                @else
                                    <!-- Fallback Store Options -->
                                    <div style="
                                        border: 2px solid #e5e7eb; 
                                        border-radius: 0.5rem; 
                                        padding: 1.5rem; 
                                        cursor: pointer;
                                        transition: all 0.2s;
                                        background-color: white;
                                    " @if($selectedStore === 'store1') style="border-color: #9333ea; background-color: #faf5ff;" @endif wire:click="selectStore('store1')">
                                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                                            <div style="
                                                width: 3rem; 
                                                height: 3rem; 
                                                background-color: #dbeafe; 
                                                border-radius: 50%; 
                                                display: flex; 
                                                align-items: center; 
                                                justify-content: center;
                                            ">
                                                <svg style="width: 1.5rem; height: 1.5rem; color: #3b82f6;" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 style="font-weight: 600; color: #111827; margin: 0;">Store 1</h4>
                                                <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Main Online Store</p>
                                            </div>
                                        </div>
                                        <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Primary e-commerce store for your products</p>
                                    </div>
                                    
                                    <div style="
                                        border: 2px solid #e5e7eb; 
                                        border-radius: 0.5rem; 
                                        padding: 1.5rem; 
                                        cursor: pointer;
                                        transition: all 0.2s;
                                        background-color: white;
                                    " @if($selectedStore === 'store2') style="border-color: #9333ea; background-color: #faf5ff;" @endif wire:click="selectStore('store2')">
                                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                                            <div style="
                                                width: 3rem; 
                                                height: 3rem; 
                                                background-color: #fef3c7; 
                                                border-radius: 50%; 
                                                display: flex; 
                                                align-items: center; 
                                                justify-content: center;
                                            ">
                                                <svg style="width: 1.5rem; height: 1.5rem; color: #f59e0b;" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 style="font-weight: 600; color: #111827; margin: 0;">Store 2</h4>
                                                <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Secondary Store</p>
                                            </div>
                                        </div>
                                        <p style="color: #6b7280; font-size: 0.875rem; margin: 0;">Secondary store for specific product categories</p>
                                    </div>
                                @endif
                            </div>
                            
                            @error('selectedStore') 
                                <div style="color: #ef4444; font-size: 0.875rem; margin-top: 1rem;">
                                    {{ $message }}
                                </div>
                            @enderror
                            
                            <!-- Custom Store Details Form -->
                            <!-- @if($selectedStore === 'new_store')
                                <div style="
                                    background-color: white; 
                                    border: 1px solid #e5e7eb; 
                                    border-radius: 0.5rem; 
                                    padding: 1.5rem; 
                                    margin-top: 2rem;
                                ">
                                    <h4 style="font-size: 1rem; font-weight: 600; color: #111827; margin-bottom: 1.5rem;">Custom Store Details</h4>
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                                        <div>
                                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                                Store Name *
                                            </label>
                                            <input wire:model="storeDetails.store_name" type="text" placeholder="Enter store name" style="
                                                width: 100%; 
                                                padding: 0.75rem; 
                                                border: 1px solid #d1d5db; 
                                                border-radius: 0.375rem; 
                                                font-size: 0.875rem;
                                                background-color: white;
                                            ">
                                            @error('storeDetails.store_name') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                                Store Type
                                            </label>
                                            <select wire:model="storeDetails.store_type" style="
                                                width: 100%; 
                                                padding: 0.75rem; 
                                                border: 1px solid #d1d5db; 
                                                border-radius: 0.375rem; 
                                                font-size: 0.875rem;
                                                background-color: white;
                                            ">
                                                <option value="">Select store type</option>
                                                <option value="main">Main Store</option>
                                                <option value="secondary">Secondary Store</option>
                                                <option value="marketplace">Marketplace</option>
                                                <option value="wholesale">Wholesale</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>
                                        <div style="grid-column: 1 / -1;">
                                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                                Store URL
                                            </label>
                                            <input wire:model="storeDetails.store_url" type="url" placeholder="Enter store URL (optional)" style="
                                                width: 100%; 
                                                padding: 0.75rem; 
                                                border: 1px solid #d1d5db; 
                                                border-radius: 0.375rem; 
                                                font-size: 0.875rem;
                                                background-color: white;
                                            ">
                                        </div>
                                        <div style="grid-column: 1 / -1;">
                                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                                Store Description
                                            </label>
                                            <textarea wire:model="storeDetails.store_description" placeholder="Enter store description (optional)" style="
                                                width: 100%; 
                                                padding: 0.75rem; 
                                                border: 1px solid #d1d5db; 
                                                border-radius: 0.375rem; 
                                                font-size: 0.875rem;
                                                background-color: white;
                                                resize: vertical;
                                                min-height: 2.5rem;
                                            "></textarea>
                                        </div>
                                    </div>
                                </div>
                            @endif -->
                        </div>
                    </div>

                @elseif($currentStep === 3)
                    <!-- Step 3: API keys -->
                    <div style="display: flex; flex-direction: column; gap: 2rem;">
                        <!-- KatanaPIM Section -->
                        <div style="
                            background-color: white; 
                            border: 1px solid #e5e7eb; 
                            border-radius: 0.5rem; 
                            padding: 1.5rem;
                        ">
                            <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 1.5rem;">KatanaPIM</h3>
                            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                        KatanaPIM URL
                                    </label>
                                    <input wire:model="katanaPimUrl" type="url" placeholder="Enter KatanaPIM URL" style="
                                        width: 100%; 
                                        padding: 0.75rem; 
                                        border: 1px solid #d1d5db; 
                                        border-radius: 0.375rem; 
                                        font-size: 0.875rem;
                                        background-color: white;
                                    ">
                                    @error('katanaPimUrl') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                        API-key KatanaPIM
                                    </label>
                                    <input wire:model="katanaPimApiKey" type="text" placeholder="Enter KatanaPIM API key" style="
                                        width: 100%; 
                                        padding: 0.75rem; 
                                        border: 1px solid #d1d5db; 
                                        border-radius: 0.375rem; 
                                        font-size: 0.875rem;
                                        background-color: white;
                                    ">
                                    @error('katanaPimApiKey') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Webshop Section -->
                        <div style="
                            background-color: white; 
                            border: 1px solid #e5e7eb; 
                            border-radius: 0.5rem; 
                            padding: 1.5rem;
                        ">
                            <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 1.5rem;">Webshop</h3>
                            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                        Webshop URL
                                    </label>
                                    <input wire:model="webshopUrl" type="url" placeholder="Enter webshop URL" style="
                                        width: 100%; 
                                        padding: 0.75rem; 
                                        border: 1px solid #d1d5db; 
                                        border-radius: 0.375rem; 
                                        font-size: 0.875rem;
                                        background-color: white;
                                    ">
                                    @error('webshopUrl') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                        API-key customer WooCommerce
                                    </label>
                                    <input wire:model="wooCommerceApiKey" type="text" placeholder="Enter WooCommerce API key" style="
                                        width: 100%; 
                                        padding: 0.75rem; 
                                        border: 1px solid #d1d5db; 
                                        border-radius: 0.375rem; 
                                        font-size: 0.875rem;
                                        background-color: white;
                                    ">
                                    @error('wooCommerceApiKey') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                        API-key secret WooCommerce
                                    </label>
                                    <input wire:model="wooCommerceApiSecret" type="text" placeholder="Enter WooCommerce API secret" style="
                                        width: 100%; 
                                        padding: 0.75rem; 
                                        border: 1px solid #d1d5db; 
                                        border-radius: 0.375rem; 
                                        font-size: 0.875rem;
                                        background-color: white;
                                    ">
                                    @error('wooCommerceApiSecret') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                @elseif($currentStep === 4)
                    <!-- Step 4: Fields -->
                    <div style="display: flex; flex-direction: column; gap: 2rem;">
                        <div>
                            <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Fields</h3>
                            <p style="color: #6b7280; margin-bottom: 2rem;">Map internal fields to external data sources.</p>
                            
                            <!-- Unique Identifier Section -->
                            <div style="margin-bottom: 2rem;">
                                <h4 style="font-size: 1rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Unique Identifier</h4>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                            Unique Identifier
                                </label>
                                        <select wire:model="uniqueIdentifier" style="
                                    width: 100%; 
                                            padding: 0.75rem; 
                                    border: 1px solid #d1d5db; 
                                    border-radius: 0.375rem; 
                                    font-size: 0.875rem;
                                            background-color: white;
                                ">
                                            <option value="SKU-1">SKU-1</option>
                                            <option value="GTIN">GTIN</option>
                                            <option value="Externalkey">Externalkey</option>
                                        </select>
                            </div>
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                        Identification Type
                                    </label>
                                    <select wire:model="identificationType" style="
                                        width: 100%; 
                                            padding: 0.75rem; 
                                        border: 1px solid #d1d5db; 
                                        border-radius: 0.375rem; 
                                        font-size: 0.875rem;
                                            background-color: white;
                                    ">
                                            <option value="SKU-1" selected>SKU-1</option>
                                    </select>
                                </div>
                                </div>
                            </div>

                            <!-- Internal Fields Section -->
                                <div>
                                <h4 style="font-size: 1rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Internal Fields</h4>
                                <div style="display: flex; flex-direction: column; gap: 1rem;">
                                    <!-- Field mappings -->
                                    <div style="display: grid; grid-template-columns: 1fr auto auto; gap: 1rem; align-items: center;">
                                        <span style="font-size: 0.875rem; color: #374151;">Name</span>
                                        <select wire:model="selectValue1" style="
                                            padding: 0.5rem; 
                                        border: 1px solid #d1d5db; 
                                        border-radius: 0.375rem; 
                                        font-size: 0.875rem;
                                        background-color: white;
                                            min-width: 250px;
                                        ">
                                            <option value="SKU-1">SKU-1</option>
                                        </select>
                                        <button type="button" style="
                                            background: none; 
                                            border: none; 
                                            color: #ef4444; 
                                            cursor: pointer;
                                            padding: 0.25rem;
                                        ">✕</button>
                                </div>
                                    
                                    <div style="display: grid; grid-template-columns: 1fr auto auto; gap: 1rem; align-items: center;">
                                        <span style="font-size: 0.875rem; color: #374151;">Description</span>
                                        <select wire:model="selectValue2" style="
                                            padding: 0.5rem; 
                                            border: 1px solid #d1d5db; 
                                            border-radius: 0.375rem; 
                                            font-size: 0.875rem;
                                            background-color: white;
                                            min-width: 250px;
                                        ">
                                            <option value="SKU-1">SKU-1</option>
                                        </select>
                                        <button type="button" style="
                                            background: none; 
                                            border: none; 
                                            color: #ef4444; 
                                            cursor: pointer;
                                            padding: 0.25rem;
                                        ">✕</button>
                            </div>
                                    
                                    <div style="display: grid; grid-template-columns: 1fr auto auto; gap: 1rem; align-items: center;">
                                        <span style="font-size: 0.875rem; color: #374151;">Price</span>
                                        <select wire:model="selectValue3" style="
                                            padding: 0.5rem; 
                                            border: 1px solid #d1d5db; 
                                            border-radius: 0.375rem; 
                                            font-size: 0.875rem;
                                            background-color: white;
                                            min-width: 250px;
                                        ">
                                            <option value="SKU-1">SKU-1</option>
                                        </select>
                                        <button type="button" style="
                                            background: none; 
                                            border: none; 
                                            color: #ef4444; 
                                            cursor: pointer;
                                            padding: 0.25rem;
                                        ">✕</button>
                        </div>
                                    
                                    <div style="display: grid; grid-template-columns: 1fr auto auto; gap: 1rem; align-items: center;">
                                        <span style="font-size: 0.875rem; color: #374151;">Quantity</span>
                                        <select wire:model="selectValue4" style="
                                            padding: 0.5rem; 
                                            border: 1px solid #d1d5db; 
                                            border-radius: 0.375rem; 
                                            font-size: 0.875rem;
                                            background-color: white;
                                            min-width: 250px;
                                        ">
                                            <option value="SKU-1">SKU-1</option>
                                        </select>
                                        <button type="button" style="
                                            background: none; 
                                            border: none; 
                                            color: #ef4444; 
                                            cursor: pointer;
                                            padding: 0.25rem;
                                        ">✕</button>
                    </div>

                                    <div style="display: grid; grid-template-columns: 1fr auto auto; gap: 1rem; align-items: center;">
                                        <span style="font-size: 0.875rem; color: #374151;">Category</span>
                                        <select wire:model="selectValue5" style="
                                            padding: 0.5rem; 
                                            border: 1px solid #d1d5db; 
                                            border-radius: 0.375rem; 
                                            font-size: 0.875rem;
                                            background-color: white;
                                            min-width: 250px;
                                        ">
                                            <option value="SKU-1">SKU-1</option>
                                        </select>
                                        <button type="button" style="
                                            background: none; 
                                            border: none; 
                                            color: #ef4444; 
                                            cursor: pointer;
                                            padding: 0.25rem;
                                        ">✕</button>
                                    </div>
                                    
                                    <div style="display: grid; grid-template-columns: 1fr auto auto; gap: 1rem; align-items: center;">
                                        <span style="font-size: 0.875rem; color: #374151;">Brand</span>
                                        <select wire:model="selectValue6" style="
                                            padding: 0.5rem; 
                                            border: 1px solid #d1d5db; 
                                            border-radius: 0.375rem; 
                                            font-size: 0.875rem;
                                            background-color: white;
                                            min-width: 250px;
                                        ">
                                            <option value="SKU-1">SKU-1</option>
                                        </select>
                                        <button type="button" style="
                                            background: none; 
                                            border: none; 
                                            color: #ef4444; 
                                    cursor: pointer;
                                            padding: 0.25rem;
                                        ">✕</button>
                                    </div>
                                    
                                    <div style="display: grid; grid-template-columns: 1fr auto auto; gap: 1rem; align-items: center;">
                                        <span style="font-size: 0.875rem; color: #374151;">Weight</span>
                                        <select wire:model="selectValue7" style="
                                            padding: 0.5rem; 
                                            border: 1px solid #d1d5db; 
                                            border-radius: 0.375rem; 
                                            font-size: 0.875rem;
                                    background-color: white;
                                            min-width: 250px;
                                        ">
                                            <option value="SKU-1">SKU-1</option>
                                        </select>
                                        <button type="button" style="
                                            background: none; 
                                            border: none; 
                                            color: #ef4444; 
                                            cursor: pointer;
                                            padding: 0.25rem;
                                        ">✕</button>
                                        </div>
                                    
                                    <div style="display: grid; grid-template-columns: 1fr auto auto; gap: 1rem; align-items: center;">
                                        <span style="font-size: 0.875rem; color: #374151;">Length</span>
                                        <select wire:model="selectValue8" style="
                                            padding: 0.5rem; 
                                            border: 1px solid #d1d5db; 
                                            border-radius: 0.375rem; 
                                            font-size: 0.875rem;
                                            background-color: white;
                                            min-width: 250px;
                                        ">
                                            <option value="SKU-1">SKU-1</option>
                                        </select>
                                        <button type="button" style="
                                            background: none; 
                                            border: none; 
                                            color: #ef4444; 
                                            cursor: pointer;
                                            padding: 0.25rem;
                                        ">✕</button>
                                        </div>
                                    
                                    <div style="display: grid; grid-template-columns: 1fr auto auto; gap: 1rem; align-items: center;">
                                        <span style="font-size: 0.875rem; color: #374151;">Width</span>
                                        <select wire:model="selectValue9" style="
                                            padding: 0.5rem; 
                                            border: 1px solid #d1d5db; 
                                            border-radius: 0.375rem; 
                                            font-size: 0.875rem;
                                            background-color: white;
                                            min-width: 250px;
                                        ">
                                            <option value="SKU-1">SKU-1</option>
                                        </select>
                                        <button type="button" style="
                                            background: none; 
                                            border: none; 
                                            color: #ef4444; 
                                            cursor: pointer;
                                            padding: 0.25rem;
                                        ">✕</button>
                                </div>
                                
                                    <div style="display: grid; grid-template-columns: 1fr auto auto; gap: 1rem; align-items: center;">
                                        <span style="font-size: 0.875rem; color: #374151;">Height</span>
                                        <select wire:model="selectValue10" style="
                                            padding: 0.5rem; 
                                            border: 1px solid #d1d5db; 
                                            border-radius: 0.375rem; 
                                            font-size: 0.875rem;
                                            background-color: white;
                                            min-width: 250px;
                                        ">
                                            <option value="SKU-1">SKU-1</option>
                                        </select>
                                        <button type="button" style="
                                            background: none; 
                                            border: none; 
                                            color: #ef4444; 
                                    cursor: pointer;
                                            padding: 0.25rem;
                                        ">✕</button>
                                    </div>
                                    
                                    <div style="display: grid; grid-template-columns: 1fr auto auto; gap: 1rem; align-items: center;">
                                        <span style="font-size: 0.875rem; color: #374151;">Image URL</span>
                                        <select wire:model="selectValue11" style="
                                            padding: 0.5rem; 
                                            border: 1px solid #d1d5db; 
                                            border-radius: 0.375rem; 
                                            font-size: 0.875rem;
                                    background-color: white;
                                            min-width: 250px;
                                        ">
                                            <option value="SKU-1">SKU-1</option>
                                        </select>
                                        <button type="button" style="
                                            background: none; 
                                            border: none; 
                                            color: #ef4444; 
                                            cursor: pointer;
                                            padding: 0.25rem;
                                        ">✕</button>
                                </div>
                                
                                    <div style="display: grid; grid-template-columns: 1fr auto auto; gap: 1rem; align-items: center;">
                                        <span style="font-size: 0.875rem; color: #374151;">Product URL</span>
                                        <select wire:model="selectValue12" style="
                                            padding: 0.5rem; 
                                            border: 1px solid #d1d5db; 
                                            border-radius: 0.375rem; 
                                            font-size: 0.875rem;
                                            background-color: white;
                                            min-width: 250px;
                                        ">
                                            <option value="SKU-1">SKU-1</option>
                                        </select>
                                        <button type="button" style="
                                            background: none; 
                                            border: none; 
                                            color: #ef4444; 
                                    cursor: pointer;
                                            padding: 0.25rem;
                                        ">✕</button>
                                    </div>
                                    
                                    <div style="display: grid; grid-template-columns: 1fr auto auto; gap: 1rem; align-items: center;">
                                        <span style="font-size: 0.875rem; color: #374151;">Status</span>
                                        <select wire:model="selectValue13" style="
                                            padding: 0.5rem; 
                                            border: 1px solid #d1d5db; 
                                            border-radius: 0.375rem; 
                                            font-size: 0.875rem;
                                            background-color: white;
                                            min-width: 250px;
                                        ">
                                            <option value="SKU-1">SKU-1</option>
                                        </select>
                                        <button type="button" style="
                                            background: none; 
                                            border: none; 
                                            color: #ef4444; 
                                            cursor: pointer;
                                            padding: 0.25rem;
                                        ">✕</button>
                                    </div>
                                </div>
                            </div>
                                </div>
                    </div>

                @elseif($currentStep === 5)
                    <!-- Step 5: Specifications -->
                    <div style="display: flex; flex-direction: column; gap: 2rem;" wire:init="loadSpecificationsIfNeeded">
                        <div>
                            <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Specifications</h3>
                            <p style="color: #6b7280; margin-bottom: 2rem;">Configure product specifications and conditions for your integration.</p>
                            
                            <!-- Product Condition Section -->
                            <div style="margin-bottom: 2rem;">
                                <h4 style="font-size: 1rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Product Condition</h4>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                        <div>
                                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                            Condition
                                            </label>
                                            <div style="display: flex; gap: 0.5rem; align-items: center;">
                                                <select wire:model="condition" style="
                                                    flex: 1;
                                                    padding: 0.75rem; 
                                                    border: 1px solid #d1d5db; 
                                                    border-radius: 0.375rem; 
                                                    font-size: 0.875rem;
                                                    background-color: white;
                                                ">
                                                    <option value="">Select condition</option>
                                                    @if(empty($specifications))
                                                        <option value="new">New</option>
                                                        <option value="used">Used</option>
                                                        <option value="refurbished">Refurbished</option>
                                                        <option value="other">Other</option>
                                                    @else
                                                        @foreach($specifications as $spec)
                                                            <option value="{{ $spec['Name'] }}">{{ $spec['Name'] }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <button type="button" wire:click="fetchSpecifications" wire:loading.attr="disabled" style="
                                                    padding: 0.5rem 1rem;
                                                    background-color: #3b82f6;
                                                    color: white;
                                                    border: none;
                                                    border-radius: 0.375rem;
                                                    font-size: 0.875rem;
                                                    cursor: pointer;
                                                    white-space: nowrap;
                                                " wire:loading.class="opacity-50">
                                                    <span wire:loading.remove>Load from API</span>
                                                    <span wire:loading>Loading...</span>
                                                </button>
                                            </div>
                                            @error('condition') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                            @error('specifications') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                            Condition Value
                                            </label>
                                        <input wire:model="conditionValue" type="text" placeholder="Enter condition value" style="
                                                width: 100%; 
                                                padding: 0.75rem; 
                                                border: 1px solid #d1d5db; 
                                                border-radius: 0.375rem; 
                                                font-size: 0.875rem;
                                                background-color: white;
                                            ">
                                        @error('conditionValue') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                        </div>
                                </div>
                            </div>

                            <!-- SEO Section -->
                            <div style="margin-bottom: 2rem;">
                                <h4 style="font-size: 1rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">SEO Settings</h4>
                                <div style="display: flex; flex-direction: column; gap: 1rem;">
                                    <div>
                                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                            Meta Title
                                            </label>
                                        <input wire:model="metaTitle" type="text" placeholder="Enter meta title" style="
                                                width: 100%; 
                                                padding: 0.75rem; 
                                                border: 1px solid #d1d5db; 
                                                border-radius: 0.375rem; 
                                                font-size: 0.875rem;
                                                background-color: white;
                                            ">
                                        @error('metaTitle') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                        </div>
                                    <div>
                                            <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                            Meta Description
                                            </label>
                                        <textarea wire:model="metaDescription" placeholder="Enter meta description" style="
                                                width: 100%; 
                                                padding: 0.75rem; 
                                                border: 1px solid #d1d5db; 
                                                border-radius: 0.375rem; 
                                                font-size: 0.875rem;
                                                background-color: white;
                                                min-height: 2.5rem;
                                            "></textarea>
                                        @error('metaDescription') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                        </div>
                                    <div>
                                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                            Keywords
                                        </label>
                                        <input wire:model="keywords" type="text" placeholder="Enter keywords (comma separated)" style="
                                            width: 100%; 
                                            padding: 0.75rem; 
                                            border: 1px solid #d1d5db; 
                                            border-radius: 0.375rem; 
                                            font-size: 0.875rem;
                                            background-color: white;
                                        ">
                                        @error('keywords') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                @elseif($currentStep === 6)
                    <!-- Step 7: Let's Go 🚀 -->
                    <div style="text-align: center; padding: 2rem;">
                        <div style="
                            width: 6rem; 
                            height: 6rem; 
                            background-color: #10b981; 
                            border-radius: 50%; 
                            display: flex; 
                            align-items: center; 
                            justify-content: center; 
                            margin: 0 auto 2rem;
                        ">
                            <svg style="width: 3rem; height: 3rem; color: white;" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h3 style="font-size: 1.5rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Ready to Go! 🚀</h3>
                        <p style="color: #6b7280; font-size: 1.125rem;">Your integration is configured and ready to use.</p>
                    </div>
                @endif
            </div>

            <!-- Navigation Buttons -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 2rem;">
                <div style="display: flex; gap: 1rem;">
                    <button style="
                        background-color: #6b7280; 
                        color: white; 
                        padding: 0.75rem 1.5rem; 
                        border-radius: 0.375rem; 
                        border: none; 
                        font-weight: 500; 
                        cursor: pointer;
                    ">
                        Cancel
                    </button>
                    <button wire:click="clearProgress" style="
                        background-color: #ef4444; 
                        color: white; 
                        padding: 0.75rem 1.5rem; 
                        border-radius: 0.375rem; 
                        border: none; 
                        font-weight: 500; 
                        cursor: pointer;
                    ">
                        Clear Progress
                    </button>
                </div>
                
                <!-- Pagination -->
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <button wire:click="previousStep" style="
                        background: none; 
                        border: none; 
                        color: #6b7280; 
                        cursor: pointer; 
                        padding: 0.5rem;
                        font-size: 1.25rem;
                    " @if($currentStep <= 1) disabled @endif>
                        ←
                    </button>
                    <div style="
                        background-color: #374151; 
                        color: white; 
                        padding: 0.5rem 1rem; 
                        border-radius: 0.375rem; 
                        font-size: 0.875rem; 
                        font-weight: 500;
                    ">
                        {{ $currentStep }}/{{ count($steps) }}
                    </div>
                    <button wire:click="nextStep" style="
                        background: none; 
                        border: none; 
                        color: #6b7280; 
                        cursor: pointer; 
                        padding: 0.5rem;
                        font-size: 1.25rem;
                    " @if($currentStep >= count($steps)) disabled @endif>
                        →
                    </button>
                </div>
                
                @if($currentStep < count($steps))
                    <button wire:click="nextStep" style="
                        background-color: #9333ea; 
                        color: white; 
                        padding: 0.75rem 1.5rem; 
                        border-radius: 0.375rem; 
                        border: none; 
                        font-weight: 500; 
                        cursor: pointer;
                    ">
                        Next step
                    </button>
                @else
                    <button wire:click="saveIntegration" style="
                        background-color: #10b981; 
                        color: white; 
                        padding: 0.75rem 1.5rem; 
                        border-radius: 0.375rem; 
                        border: none; 
                        font-weight: 500; 
                        cursor: pointer;
                    ">
                        Let's Go! 🚀
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
