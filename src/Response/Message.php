<?php

namespace Alsaad\Response;

class Message
{
    /**
     * Get error messages
     *
     * @return array
     */
    static public function errorMessages()
    {
        return [
            101 => 'البيانات ناقصة',
            102 => 'اسم المستخدم غير صحيح',
            103 => 'كلمة المرور غير صحيحة',
            104 => 'خطأ بقاعدة البيانات',
            105 => 'الرصيد لا يكفي',
            106 => 'اسم المرسل غير صحيح',
            107 => 'اسم المرسل محجوب',
            108 => 'لا يوجد ارقام صالحة للارسال',
            109 => 'لا يمكن ارال لاكثر من 8 مقاطع',
            110 => 'خطأ في نتيجة الارسال',
            111 => 'الارسال مغلق',
            112 => 'الرسالة تحتوي علي كلمة محظورة',
            113 => 'الحساب غير مفعل',
            114 => 'الحساب موقوف',
            115 => 'غير مفعل الجوال',
            116 => 'غير مفعل البريد الالكتروني',
            117 => 'الرسالة فارغة ولا يمكن ارسالها',
            1015 => 'اسم المرسل فارغ',
            1014 => 'لم يتم وضع المستقبل',
            1013 => 'لم يتم وضع نص الرسالة',
            1012 => 'خطأ في التشفير',
            1011 => 'لم يتم وضع اسم المستخدم',
            1010 => 'لم يتم وضع كلمة المرور',
        ];
    }

    /**
     * @param $statusCode
     * @return bool
     */
    static public function isErrorStatusCode($statusCode)
    {
        return in_array($statusCode, array_keys(self::errorMessages()));
    }

    /**
     * @param $statusCode
     * @return string
     */
    static public function getMessage($statusCode)
    {
        $messages = self::errorMessages();
        return $messages[$statusCode];
    }
}
