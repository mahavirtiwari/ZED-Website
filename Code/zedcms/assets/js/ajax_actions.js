

function return_ajax_response(url,data,serverProcessing)
{   
    serverProcessing = (serverProcessing === undefined) ? true : false;

    var out_response=0;
    
    /*--------------------------------------------- RECHEK AND REFINE IT ------------------------------------------*/
    var datax = '';

    if(ci_csrf_enabled)    
            datax=ci_token_name+'='+ci_token_val;
    if(! data) data = '';
    datax = (data === undefined) ? datax : datax+data;
    /*--------------------------------------------------------------------------------------------------------------*/
   // console.log(datax);
   if(debug_enable) 
        console.log('ajax_action URL: '+url);
    
    $.ajax({
             type: 'POST',
             async: false,
             cache: false,    
             url: url,
             data: datax,
             processData: serverProcessing,
             success: function(response,txtStatus,xhr) 
                    {
                        
                        // -------------------------------
                        // START PARSING RESPONSE HEADER
                        //--------------------------------

                        var rheader = xhr.getAllResponseHeaders().toLowerCase().split('\n'); // Separate header elements by \n
                        
                        $.each(rheader,function(index,value){                                // For each key
                            var line =value.split(':');
                            if(line[0].toLowerCase()=='refresh'){                            // Find if key Refresh is specified
                                var refresh_target = value.split(/=(.+)?/)[1];               // Get refresh URL
                                window.location.replace(refresh_target);                     // Relocate to URL
                            }
                        });
                        
                        // -----------------------------
                        // END PARSING RESPONSE HEADER
                        // -----------------------------
                        if(debug_enable) console.log(response);
                        // the Laravel site sends application/json (already parsed by jQuery), the old site plain text
                        dta = (typeof response === 'string') ? JSON.parse(response) : response;

                        if(ci_csrf_enabled)             // If CI CSRF Protection is enabled update Global token value
                        {
                            ci_token_val=dta.token;
                            $('input[name="'+ci_token_name+'"]').val(ci_token_val);
                        }
                        
                        if(dta.hasOwnProperty('redirect'))  window.location.replace(dta.redirect);

                        out_response = dta;
                        
                        if(typeof(ciformJqsubmit)=='function')
                         ciformJqsubmit();
                        if(typeof(prepare_ajax_actions)=='function')
                        prepare_ajax_actions(); 

                    },

             error: function(e)
                  {
                    
                       if(! debug_enable) window.location.replace(error_redirect+e.status);
                   
                  }                  
          });
    return out_response;
}