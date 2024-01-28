<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    public function acceptInvitationTenantUser(Request $request)
    {
        if (! $request->hasValidSignature()) {
            // abort(401);
            return redirect()->route('home');
        }

        $tenant = Tenant::with('users:id,email')->find($request->token);

        if(!$tenant || $tenant->users->firstWhere('email', $request->email)) {
            return redirect()->route('home');
        }

        $user = User::whereEmail($request->email)->first();

        if(!$user) {
            return redirect()->route('home');
        }

        $result = $tenant->users()->attach($user);

        if(auth()->check()) {
            if(auth()->id() == $user->id) {
                return redirect()->route('tenant.dashboard', $tenant->id);
            }

            return redirect()->route('admin.dashboard')->with('message', 'Convite aceito!');
        }else{
            auth()->login($user);

            return redirect()->route('tenant.dashboard', $tenant->id);
        }
    }
}
