<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    private const ROLES = ['admin', 'editor', 'contributor', 'viewer'];

    public function index(): View
    {
        // Les comptes client (inscription publique cachée, voir Customer\AccountController)
        // reçoivent aussi le rôle 'viewer' par défaut mais n'ont rien à faire
        // dans la gestion des comptes d'équipe — ils seraient indiscernables
        // d'un vrai lecteur sans ce filtre.
        return view('admin.users.index', [
            'users' => User::where('is_customer', false)->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:'.implode(',', self::ROLES)],
        ]);

        $user = User::create($data);

        return redirect()->route('admin.users.edit', $user)->with('status', 'user-created');
    }

    public function edit(User $user): View
    {
        abort_if($user->is_customer, 404);

        return view('admin.users.edit', ['user' => $user]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_if($user->is_customer, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', 'in:'.implode(',', self::ROLES)],
        ]);

        $password = $data['password'] ?? null;
        unset($data['password']);

        // Même logique que pour la suppression ci-dessous : on évite de se
        // retrouver sans aucun compte admin, cette fois via un changement de
        // rôle plutôt qu'une suppression — même risque de blocage définitif.
        if ($user->role === 'admin' && $data['role'] !== 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->withInput()
                ->with('status', "Impossible de changer ce rôle : c'est le dernier compte administrateur.");
        }

        $user->fill($data);

        if ($password) {
            $user->password = $password;
        }

        $user->save();

        return redirect()->route('admin.users.edit', $user)->with('status', 'user-updated');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_if($user->is_customer, 404);

        if ($user->id === $request->user()->id) {
            return redirect()->route('admin.users.index')
                ->with('status', 'Impossible de supprimer votre propre compte.');
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return redirect()->route('admin.users.index')
                ->with('status', 'Impossible de supprimer le dernier compte administrateur.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'user-deleted');
    }
}
