<section id="il_content4" class="il_tab_section doc_section">
	<div class="il_tab_inner_container">												
		<div class="inner-doc-section" dir="rtl">
			<h2>תוסף דואר ישראל עבור WooCommerce</h2>
			<p>וסף דואר ישראל מקשר בין WooCommerce ובין ה - API  של דואר ישראל ומאפשר להפיק שטרי מטען למשלוחים בינ״ל של דואר ישראל, ישירות מתוך ממשק הניהול של החנות שלכם.</p>						
			<p>לאחר הפקת שטר המטען, מספר המעקב יישמר בהזמנה ופרטי המשלוח ולינק למעקב יישלחו ללקוח ויוצגו באזור האישי תחת היסטוריית הזמנות. בנוסף, תוכלו להפנות את הלקוחות, לעמוד מעקב משלוחים מפורט אצלכם בחנות במקום לאתר דואר ישראל.</p>
			<p>על מנת להשתמש בתוסף, יש צורך בחשבון בשירות ליצואן של דואר ישראל. בכדי להשיג את מפתחות לגישה ל- API, עליכם לשלוח דוא"ל לתמיכה של דואר ישראל (PostilAPISupport@malam.com) ולספק את שם המשתמש ואימייל של החשבון בדואר ישראל שלך ואת כתובת ה- IP של השרת ממנו תפנו לשירות על מנת שיוסיפו אותו לרשימה של האתרים המורשים.</p>
			<p>לאחר קבלת מפתחות הגישה, במידה ויש שאלות טכניות על השימוש בתוסף, ניתן לפנות ל<a href="https://wordpress.org/support/plugin/israel-post-for-woocommerce/" target="blank">תמיכה של התוסף ב -WordPress.org</a>.  ניתן להוריד את התוסף <a href="https://wordpress.org/plugins/israel-post-for-woocommerce/" target="blank">מעמוד התוסף בוורדפרס</a> או להתקין את התוסף מתוך האדמין של דואר ישראל.</p>			
			<p>לאחר התקנת התוסף, יש להפעיל אותו ולעבור למסך ההגדרות של התוסף:ֿ</p>						
			<img src="<?php echo wc_il_post()->plugin_dir_url()?>assets/images/il-post-settings.png">
			<p><strong>תחת הגדרות החשבון</strong>, יש להזין את שם המשתמש והסיסמה שלכם בפורטל סחר חוץ של דואר ישראל.</p>
			<p><strong>תחת הגדרות ה -API</strong>, יש להזין את הסיסמה ומפתח ייעודי לגישה אל ה API, בשלב זה אפשר לבחור מצב Sandbox לבדיקות. לאחר שמירת הפרטים ניתן  ואפשרות לשמור Log שגיאות במקרה של תקלות.</p>
			<p>לאחר בדיקת החיבור ל -API, נעבור להגדרת כתובת וערכי ברירת מחדל למשלוחים.</p>
			<img src="<?php echo wc_il_post()->plugin_dir_url()?>assets/images/il-post-default-shipping-options.png">
			<p>במסך זה ניתן להגדיר את כתובת השולח שתופיע על שטרי המטען ואת אפשרויות המשלוח המועדפות עליכם בעת הפקת שטרי מטען. הגדרות אלו הן ערכי ברירת מחדל שניתן לשנות בעת הפקת שטר מטען.</p>
			<p>נעבור להגדרות של עמוד מעקב משלוחים שם תוכלו לבחור אם להפנות לקוחות לעמוד מעקב בחנות שלכם או לשלוח את הלקוחות למעקב באתר דואר ישראל.</p>
			<img src="<?php echo wc_il_post()->plugin_dir_url()?>assets/images/il-post-tracking-page-settings.png">
			<p>על מנת להפיק שטר מטען להזמנה, יש להיכנס לעמוד ניהול ההזמנות, אפשרות אחת היא ללחוץ על האייקון של דואר ישראל בעמודת ה-  actions, או בממשק הניהול של כל הזמנה, בלחיצה על כפתור יצירת שטר מטען.</p>
			<img class="auto-width-img" src="<?php echo wc_il_post()->plugin_dir_url()?>assets/images/il-post-order-actions-panel.png">
			<img class="auto-width-img" src="<?php echo wc_il_post()->plugin_dir_url()?>assets/images/il-post-single-order-meta-box.png">
			<p>טופס יצירת שטר מטען כולל את הכתובת למשלוח, את המוצרים מההזמנה, כתובת השולח וערכי ברירת המחדל למשלוח.</p>
			<img src="<?php echo wc_il_post()->plugin_dir_url()?>assets/images/il-post-generate-label-form.png">
			<p>במקרה ואתם רוצים לפצל את המשלוח של ההזמנה למספר חבילות, ניתן לבחור פריטים לכלול בכל שטר המטען ולהפיק מספר שטרי מטען לאותה הזמנה.</p>
			<p>כמו כן, ניתן לערוך את משקלי ומחירי הפריטים, להוסיף משקל אריזה, לבחור שיטת שילוח ואפשרויות מכס ולהפיק שטר מטען.</p>
			<p>תהליך הפקת שטר המטען יכול לקחת מספר שניות ובסיומו מספר המעקב ושטר המטען יישמרו בהזמנה ותוכלו להוריד ולהדפיס את שטר המטען.</p>
			<img src="<?php echo wc_il_post()->plugin_dir_url()?>assets/images/il-post-single-order-meta-box-generated.png">
			<p>לאחר שתסמנו את ההזמנה בסטטוס נשלחה, האימייל אישור הזמנה שיישלח ללקוח יכלול את פרטי המשלוח ואת הלינק למעקב.</p>
			<img src="<?php echo wc_il_post()->plugin_dir_url()?>assets/images/il-post-tracking-email.png">
		</div>		
    </div>		
</section> 