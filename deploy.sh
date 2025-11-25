#!/bin/bash

###############################################################################
# Shiro Properties API - Deployment Script
# استخدام: ./deploy.sh
# ملاحظة: تأكد من تشغيله من مجلد المشروع الرئيسي
###############################################################################

# الألوان للعرض
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# دالة لطباعة رسائل ملونة
print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# دالة للتحقق من نجاح آخر أمر
check_status() {
    if [ $? -eq 0 ]; then
        print_success "$1"
    else
        print_error "فشل: $1"
        print_error "توقف النشر!"
        # إلغاء وضع الصيانة
        php artisan up 2>/dev/null
        exit 1
    fi
}

# بداية السكريبت
clear
echo "=========================================="
echo "🏢 Shiro Properties API"
echo "📦 Deployment Script v1.1"
echo "=========================================="
echo ""

# التحقق من وجود artisan (للتأكد أننا في مجلد Laravel)
if [ ! -f "artisan" ]; then
    print_error "ملف artisan غير موجود. تأكد من تشغيل السكريبت من مجلد المشروع الرئيسي"
    exit 1
fi

print_info "بدء عملية النشر..."
echo ""

# طلب تأكيد من المستخدم
read -p "هل أنت متأكد من المتابعة؟ (y/n): " -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    print_warning "تم إلغاء النشر"
    exit 0
fi

echo ""

# 1. تفعيل وضع الصيانة
print_info "🔒 تفعيل وضع الصيانة..."
php artisan down --message="جاري التحديث. سنعود قريباً" --retry=60 --secret="shiro2024"
check_status "تم تفعيل وضع الصيانة"

# 2. جلب آخر التحديثات من Git (إذا كان Git موجود)
if [ -d ".git" ]; then
    print_info "📥 جلب آخر التحديثات من Git..."
    git pull origin main
    check_status "تم جلب التحديثات من Git"
else
    print_warning "Git غير موجود - تخطي هذه الخطوة"
fi

# 3. تحديث Composer Dependencies
print_info "📦 تحديث Composer dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction
check_status "تم تحديث Composer dependencies"

# 4. تشغيل Database Migrations
print_info "🗄️  تشغيل Database Migrations..."
php artisan migrate --force
check_status "تم تشغيل Migrations"

# 5. مسح جميع أنواع الـ Cache
print_info "🗑️  مسح Cache..."
php artisan cache:clear > /dev/null 2>&1
php artisan config:clear > /dev/null 2>&1
php artisan route:clear > /dev/null 2>&1
php artisan view:clear > /dev/null 2>&1
php artisan clear-compiled > /dev/null 2>&1
print_success "تم مسح Cache"

# 6. إعادة بناء Cache للإنتاج
print_info "⚡ إعادة بناء Cache..."
php artisan config:cache > /dev/null 2>&1
php artisan route:cache > /dev/null 2>&1
php artisan view:cache > /dev/null 2>&1
check_status "تم إعادة بناء Cache"

# 7. Optimize
print_info "⚙️  Optimization..."
php artisan optimize > /dev/null 2>&1
composer dump-autoload --optimize > /dev/null 2>&1
check_status "تم Optimization"

# 8. ضبط الصلاحيات
print_info "🔐 ضبط صلاحيات المجلدات..."
chmod -R 775 storage bootstrap/cache
check_status "تم ضبط الصلاحيات"

# 9. إنشاء storage link (إذا لم يكن موجود)
if [ ! -L "public/storage" ]; then
    print_info "🔗 إنشاء storage link..."
    php artisan storage:link
    check_status "تم إنشاء storage link"
fi

# 10. إلغاء وضع الصيانة
print_info "✅ إلغاء وضع الصيانة..."
php artisan up
check_status "تم إلغاء وضع الصيانة"

echo ""
echo "=========================================="
print_success "تم النشر بنجاح! 🎉"
echo "=========================================="
echo ""

# عرض معلومات مفيدة
print_info "📝 معلومات مهمة:"
echo ""
echo "  🌐 الموقع: https://api.shiroproperties.com"
echo "  🔧 Admin Panel: https://api.shiroproperties.com/admin"
echo "  📊 Logs: storage/logs/laravel.log"
echo "  🔓 Bypass Maintenance: https://api.shiroproperties.com?secret=shiro2024"
echo ""

print_warning "لا تنسى:"
echo "  1. اختبار الوظائف الجديدة"
echo "  2. التحقق من Team API pagination"
echo "  3. اختبار SMTP (نسيت كلمة المرور)"
echo "  4. مراجعة الـ logs للتأكد من عدم وجود أخطاء"
echo "  5. اختبار على المتصفحات المختلفة"
echo ""

# اختبار سريع للـ API
print_info "🧪 اختبار سريع للـ API..."
response=$(curl -s -o /dev/null -w "%{http_code}" https://api.shiroproperties.com/api/static/team)
if [ "$response" = "200" ]; then
    print_success "API تعمل بشكل صحيح (HTTP 200)"
else
    print_warning "API Status Code: $response"
fi

echo ""
echo "=========================================="
echo "النشر اكتمل بنجاح!"
echo "الوقت: $(date '+%Y-%m-%d %H:%M:%S')"
echo "=========================================="

