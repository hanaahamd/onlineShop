<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',       // معرف المستخدم (بعض الأحيان قد تحتاجه لتحديد العلاقة)
        'first_name',    // الاسم الأول
        'last_name',     // الاسم الأخير
        'email',         // البريد الإلكتروني
        'country_id',    // معرف البلد
        'address',       // العنوان
        'appartment',    // الشقة (اختياري)
        'city',          // المدينة
        'state',         // الولاية
        'zip',           // الرمز البريدي
        'mobile',        // رقم الهاتف المحمول
    ];
}