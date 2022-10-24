all about auth    https://laravel.com/docs/9.x/authentication

1. install laravel project 
2. install authentication system   link:https://laravel.com/docs/9.x/authentication 
        first connnect database
        a. composer require laravel/breeze --dev
        b.php artisan breeze:install
 
        c.php artisan migrate
        d.npm install
        e.npm run dev
3. edit everythig for user login system from default login provided by laravel
4. now create admin login system 
	  1.make everything like default authentication system 
	  2.create database and model       php artisan make:model User -m   copy everythig from user.php to admin.php replace User by Admin
										 and change necessary field for  create_user_table to admins_user_table 
	  3. make seeders and tinker if you want
	  4. make controller  admin       php artisan make:controller Admin/AdminController
	  5. make route for admin in web.php   

     //admin route
5. Route::namespace('Admin')->prefix('admin')-name('admin')-group(function(){
              //login route
                Route::namespace('Auth')->group(function(){
           });
       });
6. app/Providers/RouteServiceProvider.php uncomment protected namespace 'app/http/controllers'.
        if it hasn't any add this
        protected $namespace = 'App\\Http\\Controllers'; after     public const HOME = '/dashboard';   //it just for not to use app/http/controller in page where               ituses
        and boot function like as
                        $this->routes(function () {
                     Route::prefix('api')
                      ->middleware('api')
                      ->namespace($this->namespace)
                      ->group(base_path('routes/api.php'));

                     Route::middleware('web')
                      ->namespace($this->namespace)
                      ->group(base_path('routes/web.php'));
                    });
       if it problem then use the full path of every controller in use section 
7. app/http/controllers/auth/AuthenticatedSessionController.php copy this to      app/http/controllers/Admin/Auth/AuthenticatedSessionController.php
   change as 
   <?php

   namespace App\Http\Controllers\Admin\Auth;  //0. here folder structure change

   use App\Http\Controllers\Controller;
   use App\Http\Requests\Auth\LoginRequest;
   use App\Providers\RouteServiceProvider;
   use Illuminate\Http\Request;
   use Illuminate\Support\Facades\Auth;

	   class AuthenticatedSessionController extends Controller
	   {
		/**
		 * Display the login view.
		 *
		 * @return \Illuminate\View\View
		 */
		public function create()
		{
			return view('admin.auth.login');  //1. here it goes before auth.login
		}

		/**
		 * Handle an incoming authentication request.
		 *
		 * @param  \App\Http\Requests\Auth\LoginRequest  $request
		 * @return \Illuminate\Http\RedirectResponse
		 */
		public function store(LoginRequest $request)
		{
			$request->authenticate();

			$request->session()->regenerate();

			return redirect()->intended(RouteServiceProvider::ADMIN_HOME);  //2.here it goes before HOME 
				 // 3. also change the serviceproviders as   public const ADMIN_HOME = '/admin/dashboard';  // after public const HOME = '/dashboard';

		/**
		 * Destroy an authenticated session.
		 *
		 * @param  \Illuminate\Http\Request  $request
		 * @return \Illuminate\Http\RedirectResponse
		 */
		public function destroy(Request $request)
		{
			Auth::guard('web')->logout();

			$request->session()->invalidate();

			$request->session()->regenerateToken();

			return redirect('/');
		}
	   }

8.  make view for admin             copy     view/auth/login.blade.php    to                  view/admin/auth/login.blade.php 
                                           change in page to understand it is login page like  Admin Login by change the logo
9.  now make login route for admin by changing the login group route 
	   namespace App\Http\Controllers\Admin\Auth;

	   Route::namespace('Admin')->prefix('admin')->name('admin.')->group(function(){
				  //login route
			 Route::namespace('Auth')->group(function(){
				  Route::get('login',[AuthenticatedSessionController::class, 'create'])->name('login');      
			   });       
		 });
10. now create post method for admin login by this , also change the route on admin login view page to adminlogin

	   Route::namespace('Admin')->prefix('admin')->name('admin.')->group(function(){
				  //login route
			 Route::namespace('Auth')->group(function(){
				  Route::get('login',[AuthenticatedSessionController::class, 'create'])->name('login');      
				  Route::post('login',[AuthenticatedSessionController::class, 'store'])->name('adminlogin');      //here it is
			   });       
		 });

11. make seeders for admin      
		a. php artisan make:seed AdminSeeder
		b. edit AdminSeeder as $admin=[
				'name'=>'Admin',

				'email'=>'admin@gmail.com',
				'password'=>bcrypt('password')//password=password
			];
			Admin::create($admin);
		c. add this to DatabaseSeeder file                                   $this->call(AdminSeeder::class); 

12. making guard for Admin model 
                     declare this in Admin
			protected $guard ='admin'; after use HasApiTokens, HasFactory, Notifiable;


			config/auth.php 
						   'guards' => [
				'web' => [
					'driver' => 'session',
					'provider' => 'users',
				],
				'admin' => [
					'driver' => 'session',
					'provider' => 'admins',
				],
			],
		   and 
		  'providers' => [
				'users' => [
					'driver' => 'eloquent',
					'model' => App\Models\User::class,
				],
				'admins' => [
					'driver' => 'eloquent',
					'model' => App\Models\Admin::class,
				],

13. edit middleware/RedirectIfAuthenticated 


       if (Auth::guard($guard)->check()) {
                if($guard=='admin'){
                     return redirect(RouteServiceProvider::ADMIN_HOME);
                }
                return redirect(RouteServiceProvider::HOME);
            }
14. check that the admin.adminlogin route working by giving data .   dd($request->all()); in store function of admin/auth/authticatedSessionController
15. now make AdminLoginRequest 
        change App\Http\Controllers\Admin\Auth\AuthenticatedSessionController as
        a. use App\Http\Requests\Auth\AdminLoginRequest; instead of use App\Http\Requests\Auth\LoginRequest; 
        b.    public function store(AdminLoginRequest $request) instead of     public function store(LoginRequest $request)
        c. now make App\Http\Requests\Auth\AdminLoginRequest by copy all from LoginRequest and change the name class AdminLoginRequest extends FormRequest instead of class LoginRequest extends FormRequest
        d. change  if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) { 
           instead of  if (! Auth::guard('admin')->attempt($this->only('email', 'password'), $this->boolean('remember'))) { // 2.guard('admin')-> it goes also
        e. try to login you will get 404 NOT FOUND at admin/dashboard // if everything is currect

16. create everything for dashboard 
         a. create view in view/admin/dashboard by copy everything from dashboard and write Admin Dashboard to understand it's dashboard
         b.create route for this as 
                   Route::namespace('Admin')->prefix('admin')->name('admin.')->group(function(){
                       //login route
                    Route::namespace('Auth')->group(function(){
                     Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');      
                     Route::post('login',[AuthenticatedSessionController::class, 'store'])->name('adminlogin');    
                       });
                     Route::get('dashboard',[HomeController::class,'index']);       
                   });
          C. make index function in homecontroller as
                       public function index(){
                            return view('admin.dashboard');
        
                        }
           d.but the dashboard is for the user so change it layout and structure for admin dashboard 

                       1.copy layouts folder from view to admin 
                       2. make component for admin by copy app/view/component/applayout to app/view/component/AdminLayout and chane applayout to AdminLayout to name
                             like class AdminLayout extends Component, return view('admin.layouts.app');
                       3. change admin/layouts/app so that it got AdminLayout
                       4. change  @include('admin.layouts.navigation'), all route(dashboard) to route(admin.dashboard)
                       5. make sure that admin dashboard has <x-admin-layout>
            e. now condition for login , register, dashboard interface 
                           @auth('admin')
                    <a href="{{route('admin.dashboard')}}">Admin Dashboard</a>
                    @else
                      <a href="{{route('admin.login')}}">Admin Login</a>
                    @endauth
17. now make the logout for admin define route :   Route::post('logout',[AuthenticatedSessionController::class, 'destroy'])->name('logout');  change in destroy method
                                                   as   Auth::guard('admin')->logout();
                                                   use admin.logout() everywhere there route(logout()) in navigation

18. now protect admin.dashboard by midddleware 
              a. make middleware as php artisan make:middleware AdminMiddleware
              b. conditioned as in middleware 
                                       public function handle(Request $request, Closure $next)
                                            {
                                          if(!Auth::guard('admin')->check()){
                                         return redirect()->route('admin.login');
                                           }
            
        
                                            return $next($request);
                                          }
             c. apply middleware and group the route  Route::middleware('admin')->group(function(){
             d. register the middleware in kernel.php       protected $routeMiddleware = [  'admin' => \App\Http\Middleware\AdminMiddleware::class,

19. now after login admin can go login route again to prevent it apply middleware guest in all admin/auth route  
                                  Route::middleware('guest:admin')->namespace('Auth')->group(function(){ //   Route::middleware('guest:admin')->
              Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');      
              Route::post('login',[AuthenticatedSessionController::class, 'store'])->name('adminlogin');    
           });
