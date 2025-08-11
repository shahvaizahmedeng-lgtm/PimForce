<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\Integration;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $currentStep = 1;
    public $steps = [
        'Template',
        'API keys', 
        'Mapping',
        'Internal field',
        'Specification',
        'Let\'s Go 🚀'
    ];

    public $integrationName = '';
    public $description = '';
    public $selectedStore = '';
    public $uniqueIdentifier = '';
    public $identificationType = 'SKU';
    public $condition = '';
    public $conditionValue = '';
    public $metaTitle = '';
    public $metaDescription = '';
    public $keywords = '';
    
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
        ];
        
        session(['integration_draft' => $progress]);
    }

    public function clearProgress()
    {
        session()->forget('integration_draft');
        $this->reset();
        $this->currentStep = 1;
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
                
            case 3:
                $this->validate([
                    'selectedStore' => 'required',
                ], [
                    'selectedStore.required' => 'Please select a store.',
                ]);
                break;
                
            case 4:
                // Step 4 validation is optional as these are mapping fields
                break;
                
            case 5:
                $this->validate([
                    'condition' => 'required',
                    'conditionValue' => 'required_if:condition,other',
                ], [
                    'condition.required' => 'Please select a condition.',
                    'conditionValue.required_if' => 'Please specify the condition value.',
                ]);
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
            // return redirect()->route('integrations.index');
            
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
                $fields = ['katanaPimUrl', 'katanaPimApiKey', 'webshopUrl', 'wooCommerceApiKey', 'wooCommerceApiSecret'];
                $filled = array_filter(array_map(fn($field) => !empty($this->$field), $fields));
                return count($filled) / count($fields) * 100;
                
            case 3:
                $fields = ['selectedStore'];
                $filled = array_filter(array_map(fn($field) => !empty($this->$field), $fields));
                return count($filled) / count($fields) * 100;
                
            case 4:
                // Step 4 is optional, so always show as complete if user reaches it
                return 100;
                
            case 5:
                $fields = ['condition'];
                $filled = array_filter(array_map(fn($field) => !empty($this->$field), $fields));
                return count($filled) / count($fields) * 100;
                
            default:
                return 0;
        }
    }

    public function isStepComplete($step)
    {
        return $this->getStepCompletionStatus($step) >= 100;
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
                    <!-- Step 2: API keys -->
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

                @elseif($currentStep === 3)
                    <!-- Step 3: Mapping -->
                    <div style="display: flex; flex-direction: column; gap: 2rem;">
                        <div>
                            <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Store Mapping</h3>
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                    Store *
                                </label>
                                <select wire:model="selectedStore" style="
                                    width: 100%; 
                                    padding: 0.5rem 0.75rem; 
                                    border: 1px solid #d1d5db; 
                                    border-radius: 0.375rem; 
                                    font-size: 0.875rem;
                                ">
                                    <option value="">Select store</option>
                                    <option value="store1">Store 1</option>
                                    <option value="store2">Store 2</option>
                                </select>
                                @error('selectedStore') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Unique Identifier</h3>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                        Identification Type
                                    </label>
                                    <select wire:model="identificationType" style="
                                        width: 100%; 
                                        padding: 0.5rem 0.75rem; 
                                        border: 1px solid #d1d5db; 
                                        border-radius: 0.375rem; 
                                        font-size: 0.875rem;
                                    ">
                                        <option value="SKU">SKU</option>
                                        <option value="GTIN">GTIN</option>
                                        <option value="ExternalKey">External Key</option>
                                    </select>
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                        Unique Identifier
                                    </label>
                                    <input wire:model="uniqueIdentifier" type="text" placeholder="Enter unique identifier" style="
                                        width: 100%; 
                                        padding: 0.75rem; 
                                        border: 1px solid #d1d5db; 
                                        border-radius: 0.375rem; 
                                        font-size: 0.875rem;
                                        background-color: white;
                                    ">
                                </div>
                            </div>
                        </div>
                    </div>

                @elseif($currentStep === 4)
                    <!-- Step 4: Internal field -->
                    <div style="display: flex; flex-direction: column; gap: 2rem;">
                        <div>
                            <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Unique identifier</h3>
                            <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 2rem;">
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                        SKU / Externalkey / GTIN
                                    </label>
                                    <div style="
                                        padding: 0.5rem 0.75rem; 
                                        border: 1px solid #e5e7eb; 
                                        border-radius: 0.375rem; 
                                        font-size: 0.875rem;
                                        background-color: #f9fafb;
                                        color: #374151;
                                        position: relative;
                                        min-height: 2.5rem;
                                        display: flex;
                                        align-items: center;
                                    ">
                                        SKU-1
                                        <svg style="width: 1rem; height: 1rem; position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); color: #6b7280;" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                        Artikelcode (SKU)
                                    </label>
                                    <div style="
                                        padding: 0.5rem 0.75rem; 
                                        border: 1px solid #e5e7eb; 
                                        border-radius: 0.375rem; 
                                        font-size: 0.875rem;
                                        background-color: #f9fafb;
                                        color: #374151;
                                        position: relative;
                                        min-height: 2.5rem;
                                        display: flex;
                                        align-items: center;
                                    ">
                                        SKU-1
                                        <svg style="width: 1rem; height: 1rem; position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); color: #6b7280;" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Internal fields</h3>
                            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; align-items: start;">
                                <!-- Left Column: Field Labels -->
                                <div style="display: flex; flex-direction: column; gap: 1rem;">
                                    <div style="font-weight: 600; color: #374151; height: 4rem; display: flex; align-items: flex-start; padding-top: 0.5rem;">Name</div>
                                    <div style="font-weight: 600; color: #374151; height: 4rem; display: flex; align-items: flex-start; padding-top: 0.5rem;">GTIN</div>
                                    <div style="font-weight: 600; color: #374151; height: 4rem; display: flex; align-items: flex-start; padding-top: 0.5rem;">Short description</div>
                                    <div style="font-weight: 600; color: #374151; height: 4rem; display: flex; align-items: flex-start; padding-top: 0.5rem;">Long description</div>
                                    <div style="font-weight: 600; color: #374151; height: 4rem; display: flex; align-items: flex-start; padding-top: 0.5rem;">Tax category</div>
                                    <div style="font-weight: 600; color: #374151; height: 4rem; display: flex; align-items: flex-start; padding-top: 0.5rem;">Manufacturer</div>
                                    <div style="font-weight: 600; color: #374151; height: 4rem; display: flex; align-items: flex-start; padding-top: 0.5rem;">Manufacturer Part Number</div>
                                    <div style="font-weight: 600; color: #374151; height: 4rem; display: flex; align-items: flex-start; padding-top: 0.5rem;">Price</div>
                                    <div style="font-weight: 600; color: #374151; height: 4rem; display: flex; align-items: flex-start; padding-top: 0.5rem;">Old Price</div>
                                    <div style="font-weight: 600; color: #374151; height: 4rem; display: flex; align-items: flex-start; padding-top: 0.5rem;">Special Price</div>
                                    <div style="font-weight: 600; color: #374151; height: 4rem; display: flex; align-items: flex-start; padding-top: 0.5rem;">Product cost</div>
                                    <div style="font-weight: 600; color: #374151; height: 4rem; display: flex; align-items: flex-start; padding-top: 0.5rem;">Stock quantity</div>
                                    <div style="font-weight: 600; color: #374151; height: 4rem; display: flex; align-items: flex-start; padding-top: 0.5rem;">Available Start Date</div>
                                    <div style="font-weight: 600; color: #374151; height: 4rem; display: flex; align-items: flex-start; padding-top: 0.5rem;">Available End Date</div>
                                </div>
                                
                                <!-- Right Column: Value Selectors + Select Value Dropdowns (Stacked) -->
                                <div style="display: flex; flex-direction: column; gap: 1rem; height: 100%;">
                                    <!-- Row 1: Name -->
                                    <div style="display: flex; flex-direction: column; gap: 0.5rem; height: 4rem; justify-content: center;">
                                        <div style="
                                            padding: 0.5rem 0.75rem; 
                                            border: 1px solid #e5e7eb; 
                                            border-radius: 0.375rem; 
                                            font-size: 0.875rem;
                                            background-color: #f9fafb;
                                            color: #374151;
                                            position: relative;
                                            min-height: 2.5rem;
                                            display: flex;
                                            align-items: center;
                                        ">
                                            Settings → General → Name in KatanaPIM
                                            <svg style="width: 1rem; height: 1rem; position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); color: #6b7280;" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.875rem; color: #374151; margin-bottom: 0.5rem;">Select value</label>
                                            <select wire:model="selectValue1" style="
                                                width: 100%;
                                                padding: 0.5rem 0.75rem; 
                                                border: 2px solid #9333ea; 
                                                border-radius: 0.375rem; 
                                                font-size: 0.875rem;
                                                background-color: white;
                                                color: #374151;
                                            ">
                                                <option value="">Select value</option>
                                                <option value="SKU-1">SKU-1</option>
                                                <option value="SKU-2">SKU-2</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- Row 2: GTIN -->
                                    <div style="display: flex; flex-direction: column; gap: 0.5rem; height: 4rem; justify-content: center;">
                                        <div style="
                                            padding: 0.5rem 0.75rem; 
                                            border: 1px solid #e5e7eb; 
                                            border-radius: 0.375rem; 
                                            font-size: 0.875rem;
                                            background-color: #f9fafb;
                                            color: #374151;
                                            position: relative;
                                            min-height: 2.5rem;
                                            display: flex;
                                            align-items: center;
                                        ">
                                            Settings → General → GTIN in KatanaPIM
                                            <svg style="width: 1rem; height: 1rem; position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); color: #6b7280;" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.875rem; color: #374151; margin-bottom: 0.5rem;">Select value</label>
                                            <select wire:model="selectValue2" style="
                                                width: 100%;
                                                padding: 0.5rem 0.75rem; 
                                                border: 2px solid #ef4444; 
                                                border-radius: 0.375rem; 
                                                font-size: 0.875rem;
                                                background-color: white;
                                                color: #374151;
                                            ">
                                                <option value="">Select value</option>
                                                <option value="SKU-1">SKU-1</option>
                                                <option value="SKU-2">SKU-2</option>
                                            </select>
                                        </div>
                                        <div style="color: #ef4444; font-size: 0.75rem; margin-top: -0.5rem;">Error description</div>
                                    </div>
                                    
                                    <!-- Row 3: Short description -->
                                    <div style="display: flex; flex-direction: column; gap: 0.5rem; height: 4rem; justify-content: center;">
                                        <div style="
                                            padding: 0.5rem 0.75rem; 
                                            border: 1px solid #e5e7eb; 
                                            border-radius: 0.375rem; 
                                            font-size: 0.875rem;
                                            background-color: #f9fafb;
                                            color: #374151;
                                            position: relative;
                                            min-height: 2.5rem;
                                            display: flex;
                                            align-items: center;
                                        ">
                                            Settings → General → Short description in KatanaPIM
                                            <svg style="width: 1rem; height: 1rem; position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); color: #6b7280;" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.875rem; color: #374151; margin-bottom: 0.5rem;">Select value</label>
                                            <select wire:model="selectValue3" style="
                                                width: 100%;
                                                padding: 0.5rem 0.75rem; 
                                                border: 2px solid #9333ea; 
                                                border-radius: 0.375rem; 
                                                font-size: 0.875rem;
                                                background-color: white;
                                                color: #374151;
                                            ">
                                                <option value="">Select value</option>
                                                <option value="SKU-1">SKU-1</option>
                                                <option value="SKU-2">SKU-2</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- Row 4: Long description -->
                                    <div style="display: flex; flex-direction: column; gap: 0.5rem; height: 4rem; justify-content: center;">
                                        <div style="
                                            padding: 0.5rem 0.75rem; 
                                            border: 1px solid #e5e7eb; 
                                            border-radius: 0.375rem; 
                                            font-size: 0.875rem;
                                            background-color: #f9fafb;
                                            color: #374151;
                                            position: relative;
                                            min-height: 2.5rem;
                                            display: flex;
                                            align-items: center;
                                        ">
                                            Settings → General → Long description in KatanaPIM
                                            <svg style="width: 1rem; height: 1rem; position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); color: #6b7280;" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.875rem; color: #374151; margin-bottom: 0.5rem;">Select value</label>
                                            <select wire:model="selectValue4" style="
                                                width: 100%;
                                                padding: 0.5rem 0.75rem; 
                                                border: 2px solid #9333ea; 
                                                border-radius: 0.375rem; 
                                                font-size: 0.875rem;
                                                background-color: white;
                                                color: #374151;
                                            ">
                                                <option value="">Select value</option>
                                                <option value="SKU-1">SKU-1</option>
                                                <option value="SKU-2">SKU-2</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- Row 5: Tax category -->
                                    <div style="display: flex; flex-direction: column; gap: 0.5rem; height: 4rem; justify-content: center;">
                                        <div style="
                                            padding: 0.5rem 0.75rem; 
                                            border: 1px solid #e5e7eb; 
                                            border-radius: 0.375rem; 
                                            font-size: 0.875rem;
                                            background-color: #f9fafb;
                                            color: #374151;
                                            position: relative;
                                            min-height: 2.5rem;
                                            display: flex;
                                            align-items: center;
                                        ">
                                            Settings → General → Tax category in KatanaPIM
                                            <svg style="width: 1rem; height: 1rem; position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); color: #6b7280;" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.875rem; color: #374151; margin-bottom: 0.5rem;">Select value</label>
                                            <select wire:model="selectValue5" style="
                                                width: 100%;
                                                padding: 0.5rem 0.75rem; 
                                                border: 2px solid #9333ea; 
                                                border-radius: 0.375rem; 
                                                font-size: 0.875rem;
                                                background-color: white;
                                                color: #374151;
                                            ">
                                                <option value="">Select value</option>
                                                <option value="SKU-1">SKU-1</option>
                                                <option value="SKU-2">SKU-2</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- Row 6: Manufacturer -->
                                    <div style="display: flex; flex-direction: column; gap: 0.5rem; height: 4rem; justify-content: center;">
                                        <div style="
                                            padding: 0.5rem 0.75rem; 
                                            border: 1px solid #e5e7eb; 
                                            border-radius: 0.375rem; 
                                            font-size: 0.875rem;
                                            background-color: #f9fafb;
                                            color: #374151;
                                            position: relative;
                                            min-height: 2.5rem;
                                            display: flex;
                                            align-items: center;
                                        ">
                                            Settings → General → Manufacturer in KatanaPIM
                                            <svg style="width: 1rem; height: 1rem; position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); color: #6b7280;" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.875rem; color: #374151; margin-bottom: 0.5rem;">Select value</label>
                                            <select wire:model="selectValue6" style="
                                                width: 100%;
                                                padding: 0.5rem 0.75rem; 
                                                border: 2px solid #9333ea; 
                                                border-radius: 0.375rem; 
                                                font-size: 0.875rem;
                                                background-color: white;
                                                color: #374151;
                                            ">
                                                <option value="">Select value</option>
                                                <option value="SKU-1">SKU-1</option>
                                                <option value="SKU-2">SKU-2</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- Row 7: Manufacturer Part Number -->
                                    <div style="display: flex; flex-direction: column; gap: 0.5rem; height: 4rem; justify-content: center;">
                                        <div style="
                                            padding: 0.5rem 0.75rem; 
                                            border: 1px solid #e5e7eb; 
                                            border-radius: 0.375rem; 
                                            font-size: 0.875rem;
                                            background-color: #f9fafb;
                                            color: #374151;
                                            position: relative;
                                            min-height: 2.5rem;
                                            display: flex;
                                            align-items: center;
                                        ">
                                            Settings → General → Part Number in KatanaPIM
                                            <svg style="width: 1rem; height: 1rem; position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); color: #6b7280;" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.875rem; color: #374151; margin-bottom: 0.5rem;">Select value</label>
                                            <select wire:model="selectValue7" style="
                                                width: 100%;
                                                padding: 0.5rem 0.75rem; 
                                                border: 2px solid #9333ea; 
                                                border-radius: 0.375rem; 
                                                font-size: 0.875rem;
                                                background-color: white;
                                                color: #374151;
                                            ">
                                                <option value="">Select value</option>
                                                <option value="SKU-1">SKU-1</option>
                                                <option value="SKU-2">SKU-2</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- Row 8: Price -->
                                    <div style="display: flex; flex-direction: column; gap: 0.5rem; height: 4rem; justify-content: center;">
                                        <div style="
                                            padding: 0.5rem 0.75rem; 
                                            border: 1px solid #e5e7eb; 
                                            border-radius: 0.375rem; 
                                            font-size: 0.875rem;
                                            background-color: #f9fafb;
                                            color: #374151;
                                            position: relative;
                                            min-height: 2.5rem;
                                            display: flex;
                                            align-items: center;
                                        ">
                                            Settings → Pricing → Price in KatanaPIM
                                            <svg style="width: 1rem; height: 1rem; position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); color: #6b7280;" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.875rem; color: #374151; margin-bottom: 0.5rem;">Select value</label>
                                            <select wire:model="selectValue8" style="
                                                width: 100%;
                                                padding: 0.5rem 0.75rem; 
                                                border: 2px solid #9333ea; 
                                                border-radius: 0.375rem; 
                                                font-size: 0.875rem;
                                                background-color: white;
                                                color: #374151;
                                            ">
                                                <option value="">Select value</option>
                                                <option value="SKU-1">SKU-1</option>
                                                <option value="SKU-2">SKU-2</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- Row 9: Old Price -->
                                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                        <div style="
                                            padding: 0.5rem 0.75rem; 
                                            border: 1px solid #e5e7eb; 
                                            border-radius: 0.375rem; 
                                            font-size: 0.875rem;
                                            background-color: #f9fafb;
                                            color: #374151;
                                            position: relative;
                                            min-height: 2.5rem;
                                            display: flex;
                                            align-items: center;
                                        ">
                                            Settings → Pricing → Old Price in KatanaPIM
                                            <svg style="width: 1rem; height: 1rem; position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); color: #6b7280;" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.875rem; color: #374151; margin-bottom: 0.5rem;">Select value</label>
                                            <select wire:model="selectValue9" style="
                                                width: 100%;
                                                padding: 0.5rem 0.75rem; 
                                                border: 2px solid #9333ea; 
                                                border-radius: 0.375rem; 
                                                font-size: 0.875rem;
                                                background-color: white;
                                                color: #374151;
                                            ">
                                                <option value="">Select value</option>
                                                <option value="SKU-1">SKU-1</option>
                                                <option value="SKU-2">SKU-2</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- Row 10: Special Price -->
                                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                        <div style="
                                            padding: 0.5rem 0.75rem; 
                                            border: 1px solid #e5e7eb; 
                                            border-radius: 0.375rem; 
                                            font-size: 0.875rem;
                                            background-color: #f9fafb;
                                            color: #374151;
                                            position: relative;
                                            min-height: 2.5rem;
                                            display: flex;
                                            align-items: center;
                                        ">
                                            Settings → Pricing → Special Price in KatanaPIM
                                            <svg style="width: 1rem; height: 1rem; position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); color: #6b7280;" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.875rem; color: #374151; margin-bottom: 0.5rem;">Select value</label>
                                            <select wire:model="selectValue10" style="
                                                width: 100%;
                                                padding: 0.5rem 0.75rem; 
                                                border: 2px solid #9333ea; 
                                                border-radius: 0.375rem; 
                                                font-size: 0.875rem;
                                                background-color: white;
                                                color: #374151;
                                            ">
                                                <option value="">Select value</option>
                                                <option value="SKU-1">SKU-1</option>
                                                <option value="SKU-2">SKU-2</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- Row 11: Product cost -->
                                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                        <div style="
                                            padding: 0.5rem 0.75rem; 
                                            border: 1px solid #e5e7eb; 
                                            border-radius: 0.375rem; 
                                            font-size: 0.875rem;
                                            background-color: #f9fafb;
                                            color: #374151;
                                            position: relative;
                                            min-height: 2.5rem;
                                            display: flex;
                                            align-items: center;
                                        ">
                                            Settings → Pricing → Product Cost in KatanaPIM
                                            <svg style="width: 1rem; height: 1rem; position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); color: #6b7280;" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.875rem; color: #374151; margin-bottom: 0.5rem;">Select value</label>
                                            <select wire:model="selectValue11" style="
                                                width: 100%;
                                                padding: 0.5rem 0.75rem; 
                                                border: 2px solid #9333ea; 
                                                border-radius: 0.375rem; 
                                                font-size: 0.875rem;
                                                background-color: white;
                                                color: #374151;
                                            ">
                                                <option value="">Select value</option>
                                                <option value="SKU-1">SKU-1</option>
                                                <option value="SKU-2">SKU-2</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- Row 12: Stock quantity -->
                                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                        <div style="
                                            padding: 0.5rem 0.75rem; 
                                            border: 1px solid #e5e7eb; 
                                            border-radius: 0.375rem; 
                                            font-size: 0.875rem;
                                            background-color: #f9fafb;
                                            color: #374151;
                                            position: relative;
                                            min-height: 2.5rem;
                                            display: flex;
                                            align-items: center;
                                        ">
                                            Settings → Inventory → Stock Quantity in KatanaPIM
                                            <svg style="width: 1rem; height: 1rem; position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); color: #6b7280;" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.875rem; color: #374151; margin-bottom: 0.5rem;">Select value</label>
                                            <select wire:model="selectValue12" style="
                                                width: 100%;
                                                padding: 0.5rem 0.75rem; 
                                                border: 2px solid #9333ea; 
                                                border-radius: 0.375rem; 
                                                font-size: 0.875rem;
                                                background-color: white;
                                                color: #374151;
                                            ">
                                                <option value="">Select value</option>
                                                <option value="SKU-1">SKU-1</option>
                                                <option value="SKU-2">SKU-2</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- Row 13: Available Start Date -->
                                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                        <div style="
                                            padding: 0.5rem 0.75rem; 
                                            border: 1px solid #e5e7eb; 
                                            border-radius: 0.375rem; 
                                            font-size: 0.875rem;
                                            background-color: #f9fafb;
                                            color: #374151;
                                            position: relative;
                                            min-height: 2.5rem;
                                            display: flex;
                                            align-items: center;
                                        ">
                                            Settings → Availability → Start Date in KatanaPIM
                                            <svg style="width: 1rem; height: 1rem; position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); color: #6b7280;" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.875rem; color: #374151; margin-bottom: 0.5rem;">Select value</label>
                                            <select wire:model="selectValue13" style="
                                                width: 100%;
                                                padding: 0.5rem 0.75rem; 
                                                border: 2px solid #9333ea; 
                                                border-radius: 0.375rem; 
                                                font-size: 0.875rem;
                                                background-color: white;
                                                color: #374151;
                                            ">
                                                <option value="">Select value</option>
                                                <option value="SKU-1">SKU-1</option>
                                                <option value="SKU-2">SKU-2</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- Row 14: Available End Date -->
                                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                        <div style="
                                            padding: 0.5rem 0.75rem; 
                                            border: 1px solid #e5e7eb; 
                                            border-radius: 0.375rem; 
                                            font-size: 0.875rem;
                                            background-color: #f9fafb;
                                            color: #374151;
                                            position: relative;
                                            min-height: 2.5rem;
                                            display: flex;
                                            align-items: center;
                                        ">
                                            Settings → Availability → End Date in KatanaPIM
                                            <svg style="width: 1rem; height: 1rem; position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); color: #6b7280;" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.875rem; color: #374151; margin-bottom: 0.5rem;">Select value</label>
                                            <select wire:model="selectValue14" style="
                                                width: 100%;
                                                padding: 0.5rem 0.75rem; 
                                                border: 2px solid #9333ea; 
                                                border-radius: 0.375rem; 
                                                font-size: 0.875rem;
                                                background-color: white;
                                                color: #374151;
                                            ">
                                                <option value="">Select value</option>
                                                <option value="SKU-1">SKU-1</option>
                                                <option value="SKU-2">SKU-2</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                @elseif($currentStep === 5)
                    <!-- Step 5: Specification -->
                    <div style="display: flex; flex-direction: column; gap: 2rem;">
                        <div>
                            <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">Product Specifications</h3>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                        Condition *
                                    </label>
                                    <select wire:model="condition" style="
                                        width: 100%; 
                                        padding: 0.5rem 0.75rem; 
                                        border: 1px solid #d1d5db; 
                                        border-radius: 0.375rem; 
                                        font-size: 0.875rem;
                                    ">
                                        <option value="">Select a condition</option>
                                        <option value="new">New</option>
                                        <option value="used">Used</option>
                                        <option value="refurbished">Refurbished</option>
                                        <option value="other">Other</option>
                                    </select>
                                    @error('condition') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                        Condition Value
                                    </label>
                                    <input wire:model="conditionValue" type="text" placeholder="Specify condition value" style="
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

                        <div>
                            <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">SEO & Meta Information</h3>
                            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
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
                                        resize: vertical;
                                        min-height: 2.5rem;
                                    "></textarea>
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                                        Keywords
                                    </label>
                                    <textarea wire:model="keywords" placeholder="Enter keywords (comma separated)" style="
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
                    </div>

                @elseif($currentStep === 6)
                    <!-- Step 6: Let's Go 🚀 -->
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
