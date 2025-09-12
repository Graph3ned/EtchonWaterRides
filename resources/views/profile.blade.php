<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-blue-800 dark:text-blue-300 leading-tight">
                {{ __('Profile') }}
            </h2>
        </div>
    </x-slot>

    <div class="min-h-full p-4">
        <div class="w-full rounded-lg relative overflow-hidden">
            <!-- Clean title with logo blue -->
            <h2 class="text-2xl font-bold text-center mb-6 text-[#00A3E0]">🌊 Profile Management</h2>

            <div class="py-12">
                <div class="max-w-xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    <!-- Profile Information Form -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-xl">
                        <!-- Card Header with gradient background -->
                        <div class="bg-gradient-to-r from-cyan-500 to-blue-600 p-6">
                            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                                <div class="space-y-1">
                                    <h2 class="text-xl font-bold text-white">Profile Information</h2>
                                    @if(auth()->check() && auth()->user()->userType == 1)
                                    <p class="text-white/80 text-sm">Modify your name, username, email, and password.</p>
                                    @endif
                                    @if(auth()->check() && auth()->user()->userType == 0)
                                    <p class="text-white/80 text-sm">Modify your username and email.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Form Content -->
                        <div class="p-6">
                            <livewire:profile.update-profile-information-form />
                        </div>
                    </div>
                    @if(auth()->check() && auth()->user()->userType == 0)
                    <!-- Update Password Form -->
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-xl">
                        <!-- Card Header with gradient background -->
                        <div class="bg-gradient-to-r from-cyan-500 to-blue-600 p-6">
                            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                                <div class="space-y-1">
                                    <h2 class="text-xl font-bold text-white">Update Password</h2>
                                    <p class="text-white/80 text-sm">Ensure your account is using a long, random password to stay secure.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Form Content -->
                        <div class="p-6">
                            <livewire:profile.update-password-form />
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
