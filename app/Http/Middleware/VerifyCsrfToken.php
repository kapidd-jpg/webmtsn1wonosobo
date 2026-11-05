<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Session\TokenMismatchException;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];

    /**
     * Handle an incoming request.
     *
     * Token CSRF terikat pada sesi. Ketika sesi kedaluwarsa (mis. halaman
     * dibiarkan terbuka lebih dari SESSION_LIFETIME), token yang dikirim
     * formulir sudah tidak cocok lagi dan Laravel menampilkan halaman
     * "419 Page Expired". Di sini kegagalan tersebut diubah menjadi
     * pengalaman yang lebih ramah: buka kembali portal dengan sesi baru
     * (dan formulir login terbuka) alih-alih menampilkan halaman error.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            return parent::handle($request, $next);
        } catch (TokenMismatchException $e) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Sesi Anda sudah berakhir. Silakan muat ulang halaman lalu coba lagi.',
                ], 419);
            }

            // Logout yang gagal karena sesi sudah habis tidak perlu
            // menampilkan error: pengguna memang sudah keluar.
            if ($request->is('logout')) {
                return redirect()->route('portal.index');
            }

            return redirect()
                ->route('portal.index')
                ->with('session_expired', true)
                ->withFragment('login');
        }
    }
}
