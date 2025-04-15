<!DOCTYPE html>
<html class="h-full dark" data-theme="true" data-theme-mode="dark" dir="ltr" lang="en">
<head>
    @include('layouts.head')
    <style>
        .signin-dark {
            position: relative;
        }

        @media (max-width: 1024px) {
            .signin-dark {
                position: unset;
            }

            .signin-dark-btn{
                top: 17px;
                right: 30px;
            }
        }

    </style>
</head>
 <body class="antialiased flex h-full text-base text-gray-700 dark:bg-coal-500">
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
  <!-- Page -->
  <style>
   .branded-bg {
			background-image:url('assets/media/images/2600x1600/1.png');
		}
		.dark .branded-bg {
			background-image: url('assets/media/images/2600x1600/1-dark.png');
		}
  </style>
  <div class="grid lg:grid-cols-2 grow">
   <div class="flex justify-center items-center p-8 lg:p-10 order-2 lg:order-1 signin-dark">
    <div class="absolute top-0 end-0 pt-5 signin-dark-btn">
        <div class="menu-item mb-0.5">
         <div class="menu-link">
          <span class="menu-icon me-1">
           <i class="ki-filled ki-moon">
           </i>
          </span>
          <span class="menu-title me-3 text-sm">
           Dark Mode
          </span>
          <label class="switch switch-sm">
           <input data-theme-state="dark" data-theme-toggle="true" name="check" type="checkbox" value="1">
          </label>
         </div>
        </div>
    </div>
    
    <div class="card max-w-[370px] w-full">

        @if(session('status') || $errors->any())
            <div class="card-header flex items-stretch justify-center text-sm">
                @if(session('status'))
                    <div style="color: green;">
                        {{ session('status') }}
                    </div>
                @endif
                
                @if($errors->any())
                    <div style="color: red;">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif
     <form action="{{ route('login') }}" class="card-body flex flex-col gap-5 p-10" id="sign_in_form" method="POST">
        @csrf
      <div class="text-center mb-2.5">
       <h3 class="text-lg font-medium text-gray-900 leading-none mb-2.5">
        Sign in
       </h3>
      </div>
      <div class="grid grid-cols-2 gap-2.5">
       <a class="btn btn-light btn-sm justify-center" href="#">
        <img alt="" class="size-3.5 shrink-0" src="assets/media/brand-logos/google.svg"/>
        Use Google
       </a>
       <a class="btn btn-light btn-sm justify-center" href="#">
        <img alt="" class="size-3.5 shrink-0 dark:hidden" src="assets/media/brand-logos/apple-black.svg"/>
        <img alt="" class="size-3.5 shrink-0 light:hidden" src="assets/media/brand-logos/apple-white.svg"/>
        Use Apple
       </a>
      </div>
      <div class="flex items-center gap-2">
       <span class="border-t border-gray-200 w-full">
       </span>
       <span class="text-2xs text-gray-500 font-medium uppercase">
        Or
       </span>
       <span class="border-t border-gray-200 w-full">
       </span>
      </div>
      <div class="flex flex-col gap-1">
       <label class="form-label font-normal text-gray-900">
        Email
       </label>
       <input class="input" placeholder="email@email.com" type="email" name="email" value="{{ old('email', 'misujon58262@gmail.com') }}" required autofocus/>
      </div>
      <div class="flex flex-col gap-1">
       <div class="flex items-center justify-between gap-1">
        <label class="form-label font-normal text-gray-900">
         Password
        </label>
        <a class="text-2sm link shrink-0" href="{{ route('forget.password') }}">
         Forgot Password?
        </a>
       </div>
       <div class="input" data-toggle-password="true">
        <input name="password" placeholder="Enter Password" type="password" value="123456" required/>
        <button class="btn btn-icon" data-toggle-password-trigger="true" type="button">
         <i class="ki-filled ki-eye text-gray-500 toggle-password-active:hidden">
         </i>
         <i class="ki-filled ki-eye-slash text-gray-500 hidden toggle-password-active:block">
         </i>
        </button>
       </div>
      </div>
      <label class="checkbox-group">
       <input class="checkbox checkbox-sm" name="remember" type="checkbox" value="1" checked/>
       <span class="checkbox-label">
        Remember me
       </span>
      </label>
      <button class="btn btn-primary flex justify-center grow" type="submit">
       Sign In
      </button>
     </form>
    </div>
   </div>
   <div class="lg:rounded-xl lg:border lg:border-gray-200 lg:m-5 order-1 lg:order-2 bg-top xxl:bg-center xl:bg-cover bg-no-repeat branded-bg">
    <div class="flex flex-col p-8 lg:p-16 gap-4">
     <a href="html/demo10.html">
      <img class="h-[28px] max-w-none" src="assets/media/app/mini-logo.svg"/>
     </a>
     <div class="flex flex-col gap-3">
      <h3 class="text-2xl font-semibold text-gray-900">
       Secure Access Portal
      </h3>
      <div class="text-base font-medium text-gray-600">
       A robust authentication gateway ensuring
       <br/>
       secure
       <span class="text-gray-900 font-semibold">
        efficient user access
       </span>
       to the Metronic
       <br/>
       Dashboard interface.
      </div>
     </div>
    </div>
   </div>
  </div>
  <!-- End of Page -->

  @include('layouts.script')
 </body>
</html>
