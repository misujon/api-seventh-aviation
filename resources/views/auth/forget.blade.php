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
     <form action="{{ route('password.email') }}" class="card-body flex flex-col gap-5 p-10" id="sign_in_form" method="POST">
        @csrf
      <div class="text-center mb-2.5">
       <h3 class="text-lg font-medium text-gray-900 leading-none mb-2.5">
        Forget Password
       </h3>
       <div class="flex items-center justify-center font-medium">
        <span class="text-2sm text-gray-700 me-1.5">
            Remember Password?
        </span>
        <a class="text-2sm link" href="{{ route('login') }}">
            Sign in
        </a>
       </div>
      </div>
      <div class="flex flex-col gap-1">
       <label class="form-label font-normal text-gray-900">
        Email
       </label>
       <input class="input" placeholder="email@email.com" type="email" name="email" value="" required autofocus/>
      </div>
      
      <button class="btn btn-primary flex justify-center grow" type="submit">
            Send reset link
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
