<?php

namespace LaraModule\Core\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Log;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class File extends Model
{
    use HasFactory;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'name_original',
        'folder',
        'mime',
        'total_size',
        'uploaded_size',
        'status',
        'created_by_id',
        'disk',
    ];

    /**
     * The model's default values for attributes.
     */
    protected $attributes = [
        'status' => 'initial',
        'disk' => 'public',
    ];

    /**
     * The accessors to append to the model's array form.
     */
    protected $appends = ['src'];

    /**
     * Delete hook to remove files from storage when the model is deleted.
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($file) {
            try {
                $filePath = $file->folder.$file->name;
                if (Storage::disk($file->disk)->exists($filePath)) {
                    Storage::disk($file->disk)->delete($filePath);
                }

                $thumbnailPath = $file->folder.'thumbs/'.$file->name;
                if (Storage::disk($file->disk)->exists($thumbnailPath)) {
                    Storage::disk($file->disk)->delete($thumbnailPath);
                }
            } catch (\Exception $e) {
                Log::error('File delete error. Line: '.$e->getLine());
                Log::error('Error Message: '.$e->getMessage());
                Log::error('File Object:', $file);

                throw $e;
            }
        });
    }

    /**
     * Return public asset url for given file.
     */
    protected function src(): Attribute
    {
        return Attribute::get(
            fn ($value, array $attributes) => method_exists(Storage::disk($attributes['disk']), 'url')
                ? Storage::disk($attributes['disk'])->url($attributes['folder'].$attributes['name'])
                : null
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
