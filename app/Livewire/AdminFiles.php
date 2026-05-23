<?php

namespace App\Livewire;

use App\Models\OrderFile;
use Livewire\Component;

class AdminFiles extends Component
{
    public string $filter = 'all';
    public string $search = '';

    public function render()
    {
        $q = OrderFile::with('order:id,reference', 'uploadedBy:id,name');

        if ($this->filter !== 'all') {
            $q->where('kind', $this->filter);
        }

        if ($this->search !== '') {
            $needle = $this->search;
            $q->where(function ($qq) use ($needle) {
                $qq->where('original_name', 'like', "%{$needle}%")
                   ->orWhereHas('order', fn ($qqq) => $qqq->where('reference', 'like', "%{$needle}%"));
            });
        }

        $files = $q->orderByDesc('created_at')->get();

        // KPI calculations
        $allFiles    = OrderFile::count();
        $totalSize   = OrderFile::sum('size');
        $originals   = OrderFile::where('kind', 'original')->count();
        $tuned       = OrderFile::where('kind', 'tuned')->count();

        return view('livewire.admin-files', [
            'files'      => $files,
            'totalFiles' => $allFiles,
            'totalSize'  => $this->formatBytes($totalSize),
            'originals'  => $originals,
            'tuned'      => $tuned,
        ]);
    }

    private function formatBytes(int|float $bytes): string
    {
        if ($bytes >= 1_073_741_824) return number_format($bytes / 1_073_741_824, 2).' GB';
        if ($bytes >= 1_048_576)     return number_format($bytes / 1_048_576, 1).' MB';
        if ($bytes >= 1024)          return number_format($bytes / 1024, 1).' KB';
        return $bytes.' B';
    }
}
