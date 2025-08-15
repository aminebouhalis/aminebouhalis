document.addEventListener('DOMContentLoaded', function() {
    // العناصر الرئيسية
    const menuToggle = document.getElementById('menu-toggle');
    const accountMenu = document.getElementById('accountMenu');
    const mainContent = document.getElementById('mainContent');
    
    // التحقق من وجود العناصر الأساسية
    if (!mainContent) {
        console.error('العنصر mainContent غير موجود!');
        return;
    }

    // دالة محسنة لجلب المحتوى الكامل
    async function loadFullPageContent(url, targetElement) {
        if (!targetElement) return;
        
        try {
            // عرض حالة التحميل
            targetElement.innerHTML = `
                <div class="loading-container">
                    <div class="loading-spinner"></div>
                    <p>جاري تحميل المحتوى...</p>
                </div>
            `;

            const response = await fetch(url);
            if (!response.ok) throw new Error(`خطأ في تحميل الصفحة: ${response.status}`);
            
            const htmlText = await response.text();
            
            // إنشاء iframe لعرض الصفحة كاملة
            const iframe = document.createElement('iframe');
            iframe.id = 'loaded-page-iframe';
            iframe.style.width = '100%';
            iframe.style.height = '100%';
            iframe.style.border = 'none';
            iframe.style.borderRadius = '8px';
            iframe.style.backgroundColor = 'white';
            iframe.srcdoc = htmlText;
            
            // ضبط ارتفاع الـ iframe تلقائياً
            iframe.onload = function() {
                try {
                    const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                    const body = iframeDoc.body;
                    const html = iframeDoc.documentElement;
                    
                    // حساب الارتفاع المناسب
                    const height = Math.max(
                        body.scrollHeight,
                        body.offsetHeight,
                        html.clientHeight,
                        html.scrollHeight,
                        html.offsetHeight
                    ) + 'px';
                    
                    iframe.style.height = height;
                    
                    // إضافة حدث resize للتكيف مع تغيير المحتوى
                    new ResizeObserver(() => {
                        const newHeight = Math.max(
                            body.scrollHeight,
                            body.offsetHeight,
                            html.clientHeight,
                            html.scrollHeight,
                            html.offsetHeight
                        ) + 'px';
                        iframe.style.height = newHeight;
                    }).observe(body);
                } catch (e) {
                    console.error('لا يمكن الوصول إلى محتوى iframe:', e);
                }
            };
            
            // تنظيف وإضافة الـ iframe
            targetElement.innerHTML = '';
            targetElement.appendChild(iframe);
            
        } catch (error) {
            console.error('Error loading page:', error);
            targetElement.innerHTML = `
                <div class="error-container">
                    <h3>خطأ في تحميل الصفحة</h3>
                    <p>${error.message}</p>
                    <button onclick="window.location.reload()">إعادة المحاولة</button>
                </div>
            `;
        }
    }

    // إدارة القائمة المنسدلة للحساب
    if (menuToggle && accountMenu) {
        menuToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            accountMenu.style.display = accountMenu.style.display === 'block' ? 'none' : 'block';
        });
        
        document.addEventListener('click', () => {
            accountMenu.style.display = 'none';
        });
        
        accountMenu.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }
    
    // معالجة النقر على روابط القائمة الجانبية
    document.querySelectorAll('.block a').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const title = link.textContent.trim();
            
            if (title === "Exam Guards") {
                loadFullPageContent('Exams.php', mainContent);
            } 
            if (title === "Cours") {
                loadFullPageContent('cours.php', mainContent);
            } 
            else if (title === "Assessment") {
                loadFullPageContent('choise.php', mainContent);
            }
            else {
                mainContent.innerHTML = `
                    <div class="default-content">
                        <h2>${title}</h2>
                        <p>سيتم إضافة المحتوى قريباً</p>
                    </div>
                `;
            }
        });
    });
    
    // محتوى افتراضي عند التحميل
    mainContent.innerHTML = `
        <div class="welcome-content">
            <h2>مرحباً بك في النظام</h2>
            <p>الرجاء اختيار أحد الخيارات من القائمة الجانبية</p>
        </div>
    `;
});