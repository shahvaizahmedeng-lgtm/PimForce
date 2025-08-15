<x-layouts.app :title="__('Integrations')">
    <div class="p-4 sm:p-6">
        <!-- Header Section -->
        <div class="mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Integrations</h1>
                    <p class="text-base sm:text-lg text-gray-600">Connect your data sources and automate workflows</p>
                </div>
                <a href="{{ route('integrations.create') }}" class="bg-purple-600 hover:bg-purple-700 text-black font-semibold py-2.5 px-5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create Integration
                </a>
            </div>
                    </div>

                    @if (session('message'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                            {{ session('message') }}
                </div>
                        </div>
                    @endif

                    @if($integrations->count() > 0)
            <!-- Stats Overview -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs font-medium text-gray-600">Total</p>
                            <p class="text-xl font-bold text-gray-900">{{ $integrations->total() }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs font-medium text-gray-600">Active</p>
                            <p class="text-xl font-bold text-gray-900">{{ $integrations->where('status', 'active')->count() }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs font-medium text-gray-600">Pending</p>
                            <p class="text-xl font-bold text-gray-900">{{ $integrations->where('status', 'pending')->count() }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs font-medium text-gray-600">Stores</p>
                            <p class="text-xl font-bold text-gray-900">{{ $integrations->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Integrations Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
                                    @foreach($integrations as $integration)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition-all duration-200 overflow-hidden group">
                        <!-- Header -->
                        <div class="p-4 border-b border-gray-100">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="p-2 bg-purple-100 rounded-lg">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-base font-semibold text-gray-900 group-hover:text-purple-600 transition-colors">
                                                    {{ $integration->integrationName ?: 'Unnamed Integration' }}
                                        </h3>
                                        <p class="text-xs text-gray-500">{{ $integration->created_at->format('M d, Y') }}</p>
                                    </div>
                                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                                    {{ $integration->status === 'active' ? 'bg-green-100 text-green-800' : 
                                                       ($integration->status === 'inactive' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                    {{ ucfirst($integration->status) }}
                                                </span>
                                </div>
                            </div>
                            
                            @if($integration->description)
                                <p class="text-sm text-gray-600 line-clamp-2">{{ $integration->description }}</p>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="p-4">
                            <div class="space-y-3">
                                <!-- Store Info -->
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-500">Store</span>
                                    <div class="text-sm">
                                        @if($integration->store_details && isset($integration->store_details['store_name']))
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $integration->store_details['store_name'] }}
                                            </span>
                                        @else
                                            <span class="text-gray-500">N/A</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- API Status -->
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-500">API Status</span>
                                    <div class="flex items-center">
                                        @if($integration->apiDetails && isset($integration->apiDetails['katanaPimUrl']) && isset($integration->apiDetails['webshopUrl']))
                                            <div class="flex items-center text-green-600">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span class="text-xs font-medium">Connected</span>
                                            </div>
                                        @else
                                            <div class="flex items-center text-yellow-600">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span class="text-xs font-medium">Incomplete</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Last Updated -->
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-500">Last Updated</span>
                                    <span class="text-sm text-gray-900">{{ $integration->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="px-4 py-3 bg-gray-50 border-t border-gray-100">
                            <div class="flex items-center justify-between">
                                <a href="{{ route('integrations.show', $integration) }}" class="text-purple-600 hover:text-purple-700 font-medium text-sm flex items-center group">
                                    <span>View Details</span>
                                    <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                                                <form action="{{ route('integrations.destroy', $integration) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 font-medium text-sm flex items-center group" onclick="return confirm('Are you sure you want to delete this integration?')">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                                        Delete
                                                    </button>
                                                </form>
                            </div>
                        </div>
                    </div>
                                    @endforeach
                        </div>

            <!-- Pagination -->
            @if($integrations->hasPages())
                <div class="mt-6 flex justify-center">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-4 py-3">
                            {{ $integrations->links() }}
                        </div>
                </div>
            @endif
                    @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="mx-auto h-24 w-24 rounded-full bg-purple-100 flex items-center justify-center mb-6">
                    <svg class="h-12 w-12 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">No integrations yet</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">Start building your first integration to connect your data sources and automate your workflows.</p>
                <a href="{{ route('integrations.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-semibold rounded-xl text-white bg-purple-600 hover:bg-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                    Create Your First Integration
                </a>
                        </div>
                    @endif
                </div>
</x-layouts.app>
