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
    });  document.querySelectorAll('.block a').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const title = link.textContent.trim();
        });
    });
    
           
          
    

document.addEventListener('DOMContentLoaded', function() {
    // إخفاء جميع المحتويات عند البدء
    hideAllSections();
    
    // عرض المحتوى الترحيبي افتراضياً
    document.querySelector('.welcome-content').style.display = 'block';
    
    // ربط حدث النقر على Exam Guards
    document.getElementById('gradesLink').addEventListener('click', function(e) {
        e.preventDefault();
        loadGrades();
    });
});

function hideAllSections() {
    document.querySelector('.welcome-content').style.display = 'none';
    document.querySelector('.grades-section').style.display = 'none';
    // يمكنك إضافة أقسام أخرى هنا عند الحاجة
}

async function loadGrades() {
    try {
        // إخفاء المحتوى الترحيبي وعرض قسم الدرجات
        hideAllSections();
        const gradesSection = document.querySelector('.grades-section');
        gradesSection.style.display = 'block';
        gradesSection.innerHTML = '<p>جاري تحميل البيانات...</p>';
        
        const response = await fetch('get_grades.php');
        
        if (!response.ok) {
            throw new Error('فشل في جلب البيانات');
        }
        
        const data = await response.json();
        
        if (!data.success) {
            showNoGradesMessage(data.message || 'حدث خطأ غير متوقع');
            return;
        }
        
        if (data.grades && data.grades.length > 0) {
            renderGradesTable(data.grades);
        } else {
            showNoGradesMessage('لا توجد نتائج مسجلة بعد');
        }
        
    } catch (error) {
        console.error('Network Error:', error);
        showNoGradesMessage('فشل الاتصال بالخادم. الرجاء التحقق من اتصال الشبكة.');
    }
}

function renderGradesTable(grades) {
    const gradesSection = document.querySelector('.grades-section');
    
    let html = `
        <h2>كشف النقاط الدراسية</h2>
        <div class="table-container">
            <table class="grades-table">
                <thead>
                    <tr>
                        <th>المادة</th>
                        <th>العلامة</th>
                    </tr>
                </thead>
                <tbody>`;
    
    grades.forEach(grade => {
        html += `
            <tr>
                <td>${grade.module || 'غير محدد'}</td>
                <td>${grade.mark ?? 'N/A'}</td>
            </tr>`;
    });
    
    html += `</tbody></table></div>`;
    
    gradesSection.innerHTML = html;
}

function showNoGradesMessage(message) {
    const gradesSection = document.querySelector('.grades-section');
    gradesSection.innerHTML = `
        <div class="no-grades">
            <p>${message}</p>
        </div>
    `;
}