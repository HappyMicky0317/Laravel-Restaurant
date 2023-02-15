 <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
    <link href='http://fonts.googleapis.com/css?family=Signika:600,400,300' rel='stylesheet' type='text/css'>
    <link href="codemirror.css" rel="stylesheet">
    <link href="app.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="http://www.writephponline.com/min/?f=assets/js/bootstrap.min.js,codemirror/lib/codemirror.js,codemirror/mode/clike/clike.js,codemirror/mode/php/php.js,assets/js/modernizr.custom.js,assets/js/app.js"></script>
  
    
    <div class="container">
      <div class="editor-panel">
        <div class="editor">
            
            <form id="code_frm" name="code_form" method="POST" action="">
              <div class="CodeMirror cm-s-default">
                <textarea id="editor_code" name="editor_code" rows="5" cols="60"></textarea>
              </div>
             
              <input type="submit" value="Run Code" class="btn btn-primary pull-right run_code" title="Run Code"/>

              
            </form>
          </div>
      </div>
     
    </div>
    <!-- Modal -->
    

    

    <div class="loader" style="display:none;"></div>
   
   
    
    <script type="text/javascript">
      var editor = CodeMirror.fromTextArea(document.getElementById("editor_code"), {
        lineNumbers: true,
        matchBrackets: true,
        mode: "application/x-httpd-php",
        indentUnit: 4,
        indentWithTabs: true,
        enterMode: "keep",
        tabMode: "shift"
      });
    </script>
    <div class="evil_input hide"></div>
  </body>
</html>