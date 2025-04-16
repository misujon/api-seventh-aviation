<!DOCTYPE html>
<html class="h-full dark" data-theme="true" dir="ltr" lang="en" data-theme-mode="dark">
<head>
    @include('layouts.head')
    @yield('styles')
</head>
    <body class="antialiased flex h-full text-base text-gray-700 [--tw-page-bg:var(--tw-coal-300)] [--tw-content-bg:var(--tw-light)] [--tw-content-bg-dark:var(--tw-coal-500)] [--tw-content-scrollbar-color:#e8e8e8] [--tw-header-height:60px] [--tw-sidebar-width:270px] bg-[--tw-page-bg] lg:overflow-hidden">
        <!-- Theme Mode -->
        <script>
            const defaultThemeMode = 'light'; // light|dark|system
                let themeMode;
        
                if ( document.documentElement ) {
                    if ( localStorage.getItem('theme')) {
                            themeMode = localStorage.getItem('theme');
                    } else if ( document.documentElement.hasAttribute('data-theme-mode')) {
                        themeMode = document.documentElement.getAttribute('data-theme-mode');
                    } else {
                        themeMode = defaultThemeMode;
                    }
        
                    if (themeMode === 'system') {
                        themeMode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                    }
        
                    document.documentElement.classList.add(themeMode);
                }
        </script>
        <!-- End of Theme Mode -->

        <!-- Base -->
            <div class="flex grow">
                <!-- Header -->
                <header class="flex lg:hidden items-center fixed z-10 top-0 start-0 end-0 shrink-0 bg-[--tw-page-bg] h-[--tw-header-height]" id="header">
                    <!-- Container -->
                    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
                        <a href="{{ route('home') }}">
                            {{-- <img class="size-[34px]" src="{{ asset('assets/media/app/mini-logo-circle-success.svg') }}"/> --}}
                            <img class="h-[30px] always-invert" src="{{ asset('assets/logo.png') }}"/>
                        </a>
                        <button class="btn btn-icon btn-light btn-clear btn-sm -me-2" data-drawer-toggle="#sidebar">
                            <i class="ki-filled ki-menu"></i>
                        </button>
                    </div>
                <!-- End of Container -->
                </header>
                <!-- End of Header -->

                <!-- Wrapper -->
                <div class="flex flex-col lg:flex-row grow pt-[--tw-header-height] lg:pt-0">
                
                    @include('layouts.sidebar')

                <!-- Main -->
                <div class="flex flex-col grow lg:rounded-l-xl bg-[--tw-content-bg] dark:bg-[--tw-content-bg-dark] border border-gray-300 dark:border-gray-200 lg:ms-[--tw-sidebar-width]">
                    <div class="flex flex-col grow lg:scrollable-y-auto lg:[scrollbar-width:auto] lg:light:[--tw-scrollbar-thumb-color:var(--tw-content-scrollbar-color)] pt-5" id="scrollable_content">
                        
                        @yield('content')
                        @include('layouts.footer')

                    </div>
                </div>
                <!-- End of Main -->
                </div>
                <!-- End of Wrapper -->
            </div>
            <!-- End of Base -->

        @include('layouts.script')
        @yield('scripts')
    </body>
</html>