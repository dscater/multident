<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserStoreRequest;
use App\Models\Cliente;
use App\Models\Role;
use App\Models\User;
use App\Services\RegisterUserService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Validation\Rule;

class RegisteredUserController extends Controller
{
    public function __construct(private RegisterUserService $registerUserService) {}

    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     * @return JsonResponse|RedirectResponse|Response
     */
    public function store(RegisterUserStoreRequest $request): JsonResponse|RedirectResponse|Response
    {
        DB::beginTransaction();
        try {
            $user = $this->registerUserService->crear($request->validated());
            Auth::login($user);
            DB::commit();
            if ($request->ajax()) {
                return response()->JSON([
                    "sw" => true
                ]);
            }
            return redirect(route('portal.index', absolute: false));
        } catch (\Exception $e) {
            Log::debug("ERROR: " . $e->getMessage());
            DB::rollBack();
            throw ValidationException::withMessages([
                'error' =>  $e->getMessage(),
            ]);
        }
    }
}
