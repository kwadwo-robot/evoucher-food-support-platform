<x-guest-layout>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-green-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center">
                    <svg class="h-12 w-12 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/>
                    </svg>
                </div>
                <h1 class="mt-4 text-3xl font-bold text-gray-900">eVoucher</h1>
                <p class="mt-2 text-sm text-gray-600">Reset Your Password</p>
            </div>

            <!-- Card -->
            <div class="bg-white rounded-lg shadow-md p-8">
                <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address
                        </label>
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email', $request->email) }}" 
                            required 
                            autofocus 
                            autocomplete="username"
                            class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-500 transition"
                            placeholder="you@example.com"
                        />
                        @if ($errors->has('email'))
                            <p class="mt-2 text-sm text-red-600">
                                {{ $errors->first('email') }}
                            </p>
                        @endif
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            New Password
                        </label>
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            required 
                            autocomplete="new-password"
                            class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-500 transition"
                            placeholder="••••••••"
                        />
                        @if ($errors->has('password'))
                            <p class="mt-2 text-sm text-red-600">
                                {{ $errors->first('password') }}
                            </p>
                        @endif
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm Password
                        </label>
                        <input 
                            id="password_confirmation" 
                            type="password" 
                            name="password_confirmation" 
                            required 
                            autocomplete="new-password"
                            class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-500 transition"
                            placeholder="••••••••"
                        />
                        @if ($errors->has('password_confirmation'))
                            <p class="mt-2 text-sm text-red-600">
                                {{ $errors->first('password_confirmation') }}
                            </p>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200"
                    >
                        Reset Password
                    </button>
                </form>

                <!-- Help Text -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-center text-sm text-gray-600">
                        Remember your password? 
                        <a href="{{ route('login') }}" class="text-green-600 hover:text-green-700 font-medium">
                            Sign In
                        </a>
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center text-sm text-gray-600">
                <p>eVoucher Food Support Platform</p>
                <p class="mt-1">Northamptonshire Community Food Programme</p>
            </div>
        </div>
    </div>
</x-guest-layout>
