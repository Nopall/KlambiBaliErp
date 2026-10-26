<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'phone', 'avatar', 'status', 'last_login_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_OWNER = 'owner';

    public const ROLE_PURCHASING = 'purchasing';

    public const ROLE_PRODUKSI = 'produksi';

    public const ROLE_GUDANG = 'gudang';

    public const ROLE_SALES = 'sales';

    public const ROLE_KASIR = 'kasir';

    public const ROLE_FINANCE = 'finance';

    /**
     * Map roles to Indonesian descriptive labels.
     *
     * @return array<string, string>
     */
    public static function roleLabels(): array
    {
        return [
            self::ROLE_OWNER => 'Owner / Super Admin',
            self::ROLE_PURCHASING => 'Tim Purchasing & Pengadaan',
            self::ROLE_PRODUKSI => 'Admin Produksi & QC',
            self::ROLE_GUDANG => 'Admin Gudang (Inventory)',
            self::ROLE_SALES => 'Admin Sales & CS Online',
            self::ROLE_KASIR => 'Kasir Toko (POS Offline)',
            self::ROLE_FINANCE => 'Finance & Accounting',
        ];
    }

    /**
     * Map roles to Bootstrap badge colors.
     *
     * @return array<string, string>
     */
    public static function roleBadges(): array
    {
        return [
            self::ROLE_OWNER => 'danger',
            self::ROLE_PURCHASING => 'warning text-dark',
            self::ROLE_PRODUKSI => 'secondary',
            self::ROLE_GUDANG => 'primary',
            self::ROLE_SALES => 'info text-dark',
            self::ROLE_KASIR => 'success',
            self::ROLE_FINANCE => 'dark',
        ];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getRoleLabelAttribute(): string
    {
        return self::roleLabels()[$this->role] ?? ucfirst($this->role ?? 'User');
    }

    public function getRoleBadgeAttribute(): string
    {
        return self::roleBadges()[$this->role] ?? 'secondary';
    }

    public function isOwner(): bool
    {
        return $this->role === self::ROLE_OWNER;
    }

    public function isPurchasing(): bool
    {
        return $this->role === self::ROLE_PURCHASING;
    }

    public function isProduksi(): bool
    {
        return $this->role === self::ROLE_PRODUKSI;
    }

    public function isGudang(): bool
    {
        return $this->role === self::ROLE_GUDANG;
    }

    public function isSales(): bool
    {
        return $this->role === self::ROLE_SALES;
    }

    public function isKasir(): bool
    {
        return $this->role === self::ROLE_KASIR;
    }

    public function isFinance(): bool
    {
        return $this->role === self::ROLE_FINANCE;
    }

    /**
     * Check if user has any of the specified roles.
     *
     * @param  string|array<int, string>  $roles
     */
    public function hasRole(string|array $roles): bool
    {
        if ($this->isOwner()) {
            return true;
        }

        if (is_string($roles)) {
            $roles = [$roles];
        }

        return in_array($this->role, $roles, true);
    }

    /**
     * AdminLTE User Profile Image
     */
    public function adminlte_image(): string
    {
        if ($this->avatar) {
            return asset($this->avatar);
        }

        $initials = strtoupper(substr($this->name, 0, 2));

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=283023&color=c2ae79&bold=true';
    }

    /**
     * AdminLTE User Description under name
     */
    public function adminlte_desc(): string
    {
        return $this->role_label.' • '.$this->email;
    }

    /**
     * AdminLTE User Profile URL
     */
    public function adminlte_profile_url(): string
    {
        return $this->default_route;
    }

    /**
     * Default landing route for each role.
     */
    public function getDefaultRouteAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_KASIR => route('pos.index'),
            self::ROLE_GUDANG => route('inventory.stock-overview'),
            self::ROLE_PRODUKSI => route('produksi.work-orders'),
            self::ROLE_PURCHASING => route('purchasing.po'),
            self::ROLE_SALES => route('retur.paket'),
            self::ROLE_FINANCE => route('accounting.laba-rugi'),
            default => route('home'),
        };
    }
}
