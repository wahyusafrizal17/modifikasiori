<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'section',
        'kota_id',
        'warehouse_id',
        'speedshop_id',
    ];

    public function kota()
    {
        return $this->belongsTo(Kota::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function speedshop()
    {
        return $this->belongsTo(Speedshop::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'Admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'Manager';
    }

    public function isStaf(): bool
    {
        return $this->role === 'Staf';
    }

    public function isProduksi(): bool
    {
        return $this->section === 'produksi';
    }

    public function isWarehouse(): bool
    {
        return $this->section === 'warehouse';
    }

    public function isSpeedshop(): bool
    {
        return $this->section === 'speedshop';
    }

    public function hasSection(string $section): bool
    {
        return $this->section === $section;
    }

    public function dashboardRoute(): string
    {
        if ($this->isAdmin()) {
            return 'admin.dashboard';
        }

        return match ($this->section) {
            'produksi' => 'produksi.dashboard',
            'warehouse' => 'warehouse.dashboard',
            'speedshop' => 'speedshop.dashboard',
            default => 'login',
        };
    }

    public function activeWarehouseId(): ?int
    {
        if ($this->isAdmin() && session()->has('admin_warehouse_id')) {
            return (int) session('admin_warehouse_id');
        }

        if ($this->isSpeedshop() && $this->speedshop_id) {
            return $this->speedshop?->mutasi_warehouse_id ?? $this->speedshop?->warehouse_id;
        }

        return $this->warehouse_id;
    }

    public function activeSpeedshopId(): ?int
    {
        if ($this->isSpeedshop()) {
            return $this->speedshop_id;
        }

        return null;
    }

    public function activeWarehouse(): ?Warehouse
    {
        return Warehouse::find($this->activeWarehouseId());
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
