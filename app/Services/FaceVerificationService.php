<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class FaceVerificationService
{
    private const HASH_SIZE = 16;
    private const DEFAULT_THRESHOLD = 82.0;
    private const DESCRIPTOR_THRESHOLD = 0.58;

    public function verifyDescriptor(?array $referenceDescriptor, ?array $capturedDescriptor): array
    {
        if (!$referenceDescriptor || !$capturedDescriptor) {
            return [
                'matched' => false,
                'score' => 0,
                'threshold' => round((1 - self::DESCRIPTOR_THRESHOLD) * 100, 2),
                'reason' => 'Descriptor wajah belum tersedia.',
            ];
        }

        if (count($referenceDescriptor) !== count($capturedDescriptor) || count($referenceDescriptor) < 64) {
            return [
                'matched' => false,
                'score' => 0,
                'threshold' => round((1 - self::DESCRIPTOR_THRESHOLD) * 100, 2),
                'reason' => 'Format descriptor wajah tidak valid.',
            ];
        }

        $sum = 0.0;

        foreach ($referenceDescriptor as $index => $referenceValue) {
            $delta = (float) $referenceValue - (float) $capturedDescriptor[$index];
            $sum += $delta * $delta;
        }

        $distance = sqrt($sum);
        $score = max(0, round((1 - min(1, $distance)) * 100, 2));

        return [
            'matched' => $distance <= self::DESCRIPTOR_THRESHOLD,
            'score' => $score,
            'threshold' => round((1 - self::DESCRIPTOR_THRESHOLD) * 100, 2),
            'distance' => round($distance, 4),
            'reason' => $distance <= self::DESCRIPTOR_THRESHOLD
                ? 'Wajah berhasil diverifikasi dengan face descriptor.'
                : 'Wajah tidak cocok dengan descriptor referensi akun.',
        ];
    }

    public function verify(string $referencePath, string $capturedBase64): array
    {
        if (!Storage::disk('public')->exists($referencePath)) {
            return [
                'matched' => false,
                'score' => 0,
                'threshold' => self::DEFAULT_THRESHOLD,
                'reason' => 'Foto referensi wajah tidak ditemukan.',
            ];
        }

        $referenceImage = Storage::disk('public')->get($referencePath);
        $capturedImage = $this->decodeBase64Image($capturedBase64);

        if ($capturedImage === null) {
            return [
                'matched' => false,
                'score' => 0,
                'threshold' => self::DEFAULT_THRESHOLD,
                'reason' => 'Format foto absensi tidak valid.',
            ];
        }

        $referenceHash = $this->averageHash($referenceImage);
        $capturedHash = $this->averageHash($capturedImage);

        if ($referenceHash === null || $capturedHash === null) {
            return [
                'matched' => false,
                'score' => 0,
                'threshold' => self::DEFAULT_THRESHOLD,
                'reason' => 'Gagal memproses citra wajah.',
            ];
        }

        $distance = levenshtein($referenceHash, $capturedHash);
        $maxDistance = strlen($referenceHash);
        $score = max(0, round((1 - ($distance / max(1, $maxDistance))) * 100, 2));

        return [
            'matched' => $score >= self::DEFAULT_THRESHOLD,
            'score' => $score,
            'threshold' => self::DEFAULT_THRESHOLD,
            'reason' => $score >= self::DEFAULT_THRESHOLD
                ? 'Wajah berhasil diverifikasi.'
                : 'Wajah tidak cocok dengan referensi akun.',
        ];
    }

    private function decodeBase64Image(string $payload): ?string
    {
        $parts = explode(';base64,', $payload);

        if (count($parts) === 2) {
            $payload = $parts[1];
        }

        $decoded = base64_decode(str_replace(' ', '+', $payload), true);

        return $decoded === false ? null : $decoded;
    }

    private function averageHash(string $imageBinary): ?string
    {
        $manager = ImageManager::gd();

        try {
            $image = $manager->read($imageBinary)->cover(self::HASH_SIZE, self::HASH_SIZE)->greyscale();
        } catch (\Throwable) {
            return null;
        }

        $pixels = [];
        $sum = 0;

        for ($y = 0; $y < self::HASH_SIZE; $y++) {
            for ($x = 0; $x < self::HASH_SIZE; $x++) {
                $color = $image->pickColor($x, $y);
                $value = is_array($color) ? (int) $color[0] : (int) $color;
                $pixels[] = $value;
                $sum += $value;
            }
        }

        $average = $sum / max(1, count($pixels));

        return collect($pixels)
            ->map(fn (int $pixel) => $pixel >= $average ? '1' : '0')
            ->implode('');
    }
}
