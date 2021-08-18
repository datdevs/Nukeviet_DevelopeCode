    	<!-- BEGIN: lt_ie9 --><p class="chromeframe">{LANG.chromeframe}</p><!-- END: lt_ie9 -->
        <div id="timeoutsess" class="chromeframe">
            {LANG.timeoutsess_nouser}, <a onclick="timeoutsesscancel();" href="#">{LANG.timeoutsess_click}</a>. {LANG.timeoutsess_timeout}: <span id="secField"> 60 </span> {LANG.sec}
        </div>
        <div id="openidResult" class="nv-alert" style="display:none"></div>
        <div id="openidBt" data-result="" data-redirect=""></div>
        <script src="{NV_BASE_SITEURL}themes/{TEMPLATE}/js/bootstrap.min.js"></script>

        <script src="https://www.gstatic.com/firebasejs/8.6.8/firebase-app.js"></script>
        <script src="https://www.gstatic.com/firebasejs/8.6.8/firebase-firestore.js"></script>
        <script src="https://www.gstatic.com/firebasejs/8.6.8/firebase-analytics.js"></script>

        <script>
        // Your web app's Firebase configuration
        // For Firebase JS SDK v7.20.0 and later, measurementId is optional
        var firebaseConfig = {
            apiKey: "AIzaSyCFTjQUPxswFDK364Uo8Zmb46Qew-Nxf_g",
            authDomain: "vuji-customer-license-key.firebaseapp.com",
            projectId: "vuji-customer-license-key",
            storageBucket: "vuji-customer-license-key.appspot.com",
            messagingSenderId: "976604083345",
            appId: "1:976604083345:web:6d5d4d0019fdbf694dfb30",
            measurementId: "G-QL0JJ5P333"
        };
        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);
        firebase.analytics();

        var database = firebase.firestore();

        database.collection("licenses_info").get().then((querySnapshot) => {
            querySnapshot.forEach((doc) => {
                console.log(doc.data());
            });
        });
        </script>
	</body>
</html>