<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone',
        'avatar',
        'provider',
        'provider_id',
        'is_active',
        'email_verified_at',
        'notification_prefs',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'notification_prefs' => 'array',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function pelamarProfile()
    {
        return $this->hasOne(PelamarProfile::class);
    }

    public function perusahaanProfile()
    {
        return $this->hasOne(PerusahaanProfile::class);
    }

    public function savedJobs()
    {
        return $this->hasMany(SavedJob::class);
    }

    public function companyFollows()
    {
        return $this->hasMany(CompanyFollow::class);
    }

    public function followedCompanies()
    {
        return $this->belongsToMany(PerusahaanProfile::class, 'company_follows');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->unread();
    }

    public function eventRegistrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function skillAssessmentResults()
    {
        return $this->hasMany(SkillAssessmentResult::class);
    }

    public function hasRole(string $slug): bool
    {
        return $this->role?->slug === $slug;
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(Role::SUPER_ADMIN);
    }

    public function isPerusahaan(): bool
    {
        return $this->hasRole(Role::PERUSAHAAN);
    }

    public function isPelamar(): bool
    {
        return $this->hasRole(Role::PELAMAR);
    }

    public function getRoleNameAttribute(): string
    {
        return $this->role?->name ?? 'Pengguna';
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar && file_exists(public_path('storage/' . $this->avatar))) {
            return asset('storage/' . $this->avatar);
        }
        return null;
    }

    public function getUnreadMessagesCountAttribute(): int
    {
        return Message::where('receiver_id', $this->id)->where('is_read', false)->count();
    }

    /**
     * Jenis notifikasi email yang bisa diatur user.
     * key => [label, deskripsi, untuk role apa]
     */
    public static function notificationTypes(): array
    {
        return [
            'status_lamaran' => [
                'label' => 'Perubahan Status Lamaran',
                'desc' => 'Email saat status lamaran Anda diubah perusahaan (mis. Diproses, Diterima, Ditolak).',
                'roles' => [Role::PELAMAR],
            ],
            'interview' => [
                'label' => 'Jadwal Interview',
                'desc' => 'Email saat perusahaan menjadwalkan interview untuk Anda.',
                'roles' => [Role::PELAMAR],
            ],
            'feedback' => [
                'label' => 'Feedback Perusahaan',
                'desc' => 'Email saat perusahaan mengirim penilaian / feedback hasil interview Anda.',
                'roles' => [Role::PELAMAR],
            ],
            'lowongan_baru' => [
                'label' => 'Lowongan Baru dari Perusahaan yang Diikuti',
                'desc' => 'Email saat perusahaan yang Anda ikuti memposting lowongan baru.',
                'roles' => [Role::PELAMAR],
            ],
            'verifikasi' => [
                'label' => 'Status Verifikasi Perusahaan',
                'desc' => 'Email saat status verifikasi perusahaan Anda diubah admin (lolos / ditolak).',
                'roles' => [Role::PERUSAHAAN],
            ],
        ];
    }

    /**
     * Cek apakah user masih ingin menerima email untuk tipe tertentu.
     * Default (null / belum diatur) = semua email aktif.
     */
    public function wantsEmail(string $type): bool
    {
        $prefs = $this->notification_prefs ?? [];
        if (empty($prefs)) {
            return true;
        }
        return !empty($prefs[$type]);
    }
}
