import streamlit as st
from streamlit_qrcode_scanner import qrcode_scanner
import time

st.title("♻️ فاحص WinCycle")

# فتح الكاميرا مباشرة من المتصفح
qr_code = qrcode_scanner(key='scanner')

if qr_code:
    try:
        # البيانات الموقعة: ID,Points,Weight,Material,Timestamp
        parts = qr_code.split(',')
        points = int(parts[1])
        qr_time = int(parts[4])
        
        # فحص الـ 20 ثانية
        if int(time.time()) - qr_time <= 20:
            st.success(f"✅ مبروك! حصلت على {points} نقطة")
            st.balloons()
        else:
            st.error("❌ عذراً، انتهت صلاحية الكود!")
    except:
        st.warning("⚠️ كود غير صالح")

# زر الفليكسي
if st.button("تحويل النقاط إلى رصيد"):
    # سيفتح لوحة الاتصال مباشرة
    st.markdown(f'<a href="tel:*610*0770000000*100*0000%23" target="_self">تأكيد التحويل (Djezzy)</a>', unsafe_allow_html=True)
