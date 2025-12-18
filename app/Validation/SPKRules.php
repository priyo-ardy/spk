<?php

namespace App\Validation;

/**
 * SPK Validation Rules
 * 
 * Centralized validation rules untuk SPK form
 * Menghilangkan duplikasi rules di saveData() dan updateData()
 */
class SPKRules
{
    /**
     * Base validation rules untuk SPK (tanpa image)
     * 
     * @return array<string, array<string, mixed>>
     */
    public static function getBaseRules(): array
    {
        return [
            'doc_type' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Document type is required'
                ]
            ],
            'data_lokasi' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Equipment/Machine location is required'
                ]
            ],
            'data_dept' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Requested dept is required'
                ]
            ],
            'data_pelapor' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Requested by is required'
                ]
            ],
            'data_tanggal' => [
                'rules' => 'required|valid_date',
                'errors' => [
                    'required' => 'Requested date is required',
                    'valid_date' => 'Request date must have a valid date format'
                ]
            ],
            'data_material' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Material is required'
                ]
            ],
            'data_leader' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Team leader/supervisor is required'
                ]
            ],
            'data_defect' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Problem defect is required'
                ]
            ],
            'data_sub_defect' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Problem sub defect is required'
                ]
            ],
            'data_berulang' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Repeat problem is required'
                ]
            ],
            'data_repair' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Repair reason is required'
                ]
            ],
        ];
    }

    /**
     * Image validation rules
     * 
     * @return array<string, array<string, mixed>>
     */
    public static function getImageRules(): array
    {
        return [
            'data_image' => [
                'rules' => 'uploaded[data_image]|max_size[data_image,51200]|is_image[data_image]|mime_in[data_image,image/jpg,image/jpeg,image/png]|ext_in[data_image,jpg,jpeg,png]',
                'errors' => [
                    'uploaded' => 'Problem position photo is required',
                    'max_size' => 'Problem position photo maximum size is 50MB',
                    'is_image' => 'Problem position photo must be image file (JPG/JPEG/PNG)',
                    'mime_in' => 'Problem position photo format must be JPG, JPEG or PNG',
                    'ext_in' => 'Problem position photo extension must be .jpg, .jpeg or .png'
                ]
            ]
        ];
    }

    /**
     * Equipment type rules (untuk non-mold/equipment SPK)
     * 
     * @return array<string, array<string, mixed>>
     */
    public static function getEquipmentTypeRules(): array
    {
        return [
            'tipe_equipment' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Equipment type is required'
                ]
            ]
        ];
    }

    /**
     * Get complete rules for CREATE SPK (image required)
     * 
     * @param string $docType Document type (1 = Mold, 2 = Equipment)
     * @return array<string, array<string, mixed>>
     */
    public static function getCreateRules(string $docType): array
    {
        $rules = self::getBaseRules();
        $rules = array_merge($rules, self::getImageRules());

        // Equipment type required untuk non-mold SPK
        if ($docType !== '1') {
            $rules = array_merge($rules, self::getEquipmentTypeRules());
        }

        return $rules;
    }

    /**
     * Get complete rules for UPDATE SPK (image optional)
     * 
     * @param string $docType Document type (1 = Mold, 2 = Equipment)
     * @param bool $hasImage Whether new images are being uploaded
     * @return array<string, array<string, mixed>>
     */
    public static function getUpdateRules(string $docType, bool $hasImage = false): array
    {
        $rules = self::getBaseRules();

        // Only validate image if new images are uploaded
        if ($hasImage) {
            $rules = array_merge($rules, self::getImageRules());
        }

        // Equipment type required untuk non-mold SPK
        if ($docType !== '1') {
            $rules = array_merge($rules, self::getEquipmentTypeRules());
        }

        return $rules;
    }

    /**
     * Format validation errors untuk response
     * 
     * @param array<string, string> $errors Validation errors dari CI4
     * @return string Formatted error message
     */
    public static function formatErrors(array $errors): string
    {
        return implode("<br>", array_map(function ($field, $msg) {
            return "<strong>{$field}</strong>: {$msg}";
        }, array_keys($errors), $errors));
    }

    /**
     * Check if uploaded files contain valid images
     * 
     * @param array $files Array of uploaded files
     * @return bool
     */
    public static function hasValidImages(array $files): bool
    {
        foreach ($files as $file) {
            if ($file && $file->isValid() && $file->getSize() > 0) {
                return true;
            }
        }
        return false;
    }
}
