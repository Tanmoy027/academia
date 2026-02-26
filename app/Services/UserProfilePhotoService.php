<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserProfilePhotoService
{
    public function store(User $user, UploadedFile $file): string
    {
        $this->deletePath($user->profile_picture);

        $disk = $this->disk();
        $directory = 'user-' . $user->id;
        $filename = Str::uuid()->toString() . '.' . strtolower($file->getClientOriginalExtension());

        return Storage::disk($disk)->putFileAs($directory, $file, $filename, [
            'visibility' => 'public',
        ]);
    }

    public function delete(User $user): void
    {
        $this->deletePath($user->profile_picture);
        $user->forceFill(['profile_picture' => null])->save();
    }

    private function deletePath(?string $path): void
    {
        if (!$path) {
            return;
        }

        if (Str::startsWith($path, '/storage/')) {
            Storage::disk('public')->delete(Str::after($path, '/storage/'));
            return;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            $publicBase = $this->publicBaseUrl();
            if ($publicBase && Str::startsWith($path, $publicBase)) {
                $relative = ltrim(Str::after($path, $publicBase), '/');
                Storage::disk($this->disk())->delete($relative);
            }
            return;
        }

        Storage::disk($this->disk())->delete($path);
    }

    private function disk(): string
    {
        return config('filesystems.user_profile_disk', 'public');
    }

    private function publicBaseUrl(): string
    {
        $diskConfig = config('filesystems.disks.' . $this->disk(), []);

        return isset($diskConfig['url'])
            ? rtrim($diskConfig['url'], '/')
            : '';
    }
}
