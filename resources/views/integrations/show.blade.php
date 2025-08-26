<x-layouts.app :title="__('Integration Details')">
    <div class="p-4 sm:p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Integration Details</h1>
                <p class="text-base sm:text-lg text-gray-600 mt-1">View and manage your integration settings</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('integrations.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                    Back to Integrations
                </a>
                <form action="{{ route('integrations.destroy', $integration) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-200" onclick="return confirm('Are you sure you want to delete this integration?')">
                        Delete Integration
                    </button>
                </form>
            </div>
        </div>

        @if (session('message'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                {{ session('message') }}
            </div>
        @endif

        <!-- Integration Overview -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Integration Overview</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Basic Information</h4>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Name</dt>
                                <dd class="text-sm text-gray-900 break-all">{{ $integration->integrationName ?: 'Unnamed Integration' }}</dd>
                            </div>
                            @if($integration->description)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                                    <dd class="text-sm text-gray-900">{{ $integration->description }}</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                        {{ $integration->status === 'active' ? 'bg-green-100 text-green-800' :
                                           ($integration->status === 'inactive' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($integration->status) }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Created</dt>
                                <dd class="text-sm text-gray-900">{{ $integration->created_at->format('M d, Y \a\t g:i A') }}</dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Store Configuration</h4>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Selected Store</dt>
                                <dd class="text-sm text-gray-900">
                                    @if($integration->storeName)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $integration->storeName }}
                                        </span>
                                    @else
                                        <span class="text-gray-500">N/A</span>
                                    @endif
                                </dd>
                            </div>
                            @if($integration->storeName)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Store Name</dt>
                                    <dd class="text-sm text-gray-900">{{ $integration->storeName }}</dd>
                                </div>
                            @endif
                            @if($integration->storeType)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Store Type</dt>
                                    <dd class="text-sm text-gray-900">{{ ucfirst($integration->storeType) }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- API Configuration -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">API Configuration</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">KatanaPIM</h4>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">URL</dt>
                                <dd class="text-sm text-gray-900 font-mono break-all">{{ $integration->katanaPimUrl ?: 'Not configured' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">API Key</dt>
                                <dd class="text-sm text-gray-900 font-mono">{{ $integration->katanaPimApiKey ? '••••••••••••••••' : 'Not configured' }}</dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Webshop</h4>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">URL</dt>
                                <dd class="text-sm text-gray-900 font-mono break-all">{{ $integration->webshopUrl ?: 'Not configured' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">WooCommerce API Key</dt>
                                <dd class="text-sm text-gray-900 font-mono">{{ $integration->wooCommerceApiKey ? '••••••••••••••••' : 'Not configured' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">WooCommerce API Secret</dt>
                                <dd class="text-sm text-gray-900 font-mono">{{ $integration->wooCommerceApiSecret ? '••••••••••••••••' : 'Not configured' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Field Mapping -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Field Mapping</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Unique Identifier</h4>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Identifier</dt>
                                <dd class="text-sm text-gray-900">{{ $integration->uniqueIdentifierValue ?: 'Not configured' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Type</dt>
                                <dd class="text-sm text-gray-900">{{ $integration->identificationType ?: 'Not configured' }}</dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Product Specifications</h4>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-800">Condition</dt>
                                <dd class="text-sm text-gray-900">{{ $integration->productConditionValue ?: 'Not configured' }}</dd>
                            </div>
                            @if($integration->productConditionValueString)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Condition Value</dt>
                                    <dd class="text-sm text-gray-900">{{ $integration->productConditionValueString }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
