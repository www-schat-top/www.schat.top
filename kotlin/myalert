class Common : AppCompatActivity() {
    /**
     * From: https://github.com/www-schat-top/www.schat.top/new/master/kotlin
     * @param Context ctx
     * @param String msg
     * @param String title
     * @param String btnP  the PositiveButton value
     * @param String btnN  the NegativeButton
     * @param Unit   fP    {...}
     * @param Unit   fN    {...}
     *
     * @usage: 
        Common().myalert(this@MainActivity, "message");
        
        Common().myalert(this@MainActivity, "Are you ok?", "Tip");
        
        Common().myalert(this@MainActivity, "Are you ok?", btnP="Yes");
        
        Common().myalert(this@MainActivity, "Are you ok?", "Tip", "Yes");
        
        Common().myalert(this@MainActivity, "Are you ok?", "Tip", "Yes", fP={
            println("yes i am ok ...");
        });
        
        Common().myalert(this@MainActivity, "Are you ok?", "Tip", "Yes", "No");
        
         Common().myalert(this@MainActivity, "Are you ok?", "Tip", "Yes", "No", fP={
            println("yes i am ok ...");
         }, fN = {
            println("yes i am not ok ...");
         });
     */
     
    fun myalert(ctx: Context, msg: String, title: String="", btnP: String = "OK", btnN: String = "", fP: () -> Unit = {}, fN: () -> Unit = {}) {
        runOnUiThread() {
            var alert = AlertDialog.Builder(ctx);
            
            alert.setMessage(msg);
            
            if(title != "")
                alert.setTitle(title);
                 
            alert.setPositiveButton(btnP, { dialog: DialogInterface, which: Int ->
                fP();
            })

            if (btnN != "") {
                alert.setNegativeButton(btnN, { dialog: DialogInterface, which: Int ->
                    fN();
                })
            }
            alert.show();
        }
    }
}  
