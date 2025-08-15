<x-layouts.app :title="__('Dashboard')">
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800">
        <!-- Welcome Header -->
        <div class="px-6 py-8">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-8">
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
                        Welcome back, {{ auth()->user()->name }}! 👋
                    </h1>
                    <p class="text-lg text-gray-600 dark:text-gray-300">
                        Ready to manage your product integrations today?
                    </p>
                </div>

                <!-- Quick Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center">
                            <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Integrations</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ auth()->user()->integrations()->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center">
                            <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Integrations</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ auth()->user()->integrations()->where('status', 'active')->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center">
                            <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Last Activity</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                    @if(auth()->user()->integrations()->latest()->first())
                                        {{ auth()->user()->integrations()->latest()->first()->updated_at->diffForHumans() }}
                                    @else
                                        No activity yet
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <!-- <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Quick Actions</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <a href="{{ route('integrations.create') }}" class="group relative bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 rounded-lg p-6 text-white transition-all duration-200 transform hover:scale-105">
                            <div class="flex items-center">
                                <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold">Create Integration</h3>
                                    <p class="text-blue-100 text-sm">Set up a new product integration</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('integrations.index') }}" class="group relative bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 rounded-lg p-6 text-white transition-all duration-200 transform hover:scale-105">
                            <div class="flex items-center">
                                <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold">View Integrations</h3>
                                    <p class="text-green-100 text-sm">Manage your existing integrations</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('settings.profile') }}" class="group relative bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 rounded-lg p-6 text-white transition-all duration-200 transform hover:scale-105">
                            <div class="flex items-center">
                                <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold">Profile Settings</h3>
                                    <p class="text-purple-100 text-sm">Update your account information</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div> -->

                <!-- Recent Activity -->
                <!-- @if(auth()->user()->integrations()->count() > 0)
                <div class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Recent Integrations</h2>
                    <div class="space-y-4">
                        @foreach(auth()->user()->integrations()->latest()->take(3)->get() as $integration)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center">
                                <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $integration->integrationName }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $integration->description }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-3 py-1 text-xs font-medium rounded-full 
                                    @if($integration->status === 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($integration->status === 'inactive') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                    @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @endif">
                                    {{ ucfirst($integration->status) }}
                                </span>
                                <a href="{{ route('integrations.show', $integration) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @if(auth()->user()->integrations()->count() > 3)
                    <div class="mt-6 text-center">
                        <a href="{{ route('integrations.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                            View all integrations →
                        </a>
                    </div>
                    @endif
                </div>
                @endif -->

                <!-- Welcome Message for New Users -->
                @if(auth()->user()->integrations()->count() === 0)
                <div class="mt-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl p-8 text-white text-center">
                    <div class="max-w-2xl mx-auto">
                        <div class="mb-4">
                            <svg class="w-16 h-16 mx-auto text-white opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h2 class="text-3xl font-bold mb-4">Get Started with Your First Integration</h2>
                        <p class="text-xl mb-6 opacity-90">
                            Welcome to PimForce! Create your first product integration to start managing your products efficiently.
                        </p>
                        <a href="{{ route('integrations.create') }}" class="inline-flex items-center px-6 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-gray-100 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Create Your First Integration
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
