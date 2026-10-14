<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class frontController extends Controller
{
    public function getLogin(){
        // Se l'utente è già loggato, reindirizza direttamente alla dashboard corretta
        if (Auth::check()) {
            return $this->redirectUserByRole(Auth::user());
        }

        return view('utente.login');
    }

    /**
     * Gestisce l'invio del form di login (POST).
     */
    public function postLogin(Request $request){
        $hidden = [$request->input('password')];
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            $hidden => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Reindirizza in base al ruolo dell'utente loggato
            return $this->redirectUserByRole(Auth::user());
        }

        return back()->withErrors([
            'username' => 'Credenziali non valide.',
        ])->onlyInput('username');
    }

    /**
     * Reindirizza l'utente alla vista corrispondente al suo ruolo.
     */
    private function redirectUserByRole($user){
        if ($user->ruolo === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('docente.dashboard');
    }

    /**
     * Effettua il logout e distrugge la sessione.
     */
    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}