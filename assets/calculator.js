(function($) {
    
 
    $('#wpr-calculator select').on('change',function(){

        var slider = $('#perioada');
        var slider2 = $('#suma');
        $('#perioada').empty();
        $('#suma').empty();
        $('#one').empty();
        $('#two').empty();
        $('#wpr-calculator-results').empty();
        // console.log($(this).find(':selected').data('max'));
        slider.attr('max', $(this).find(':selected').data('max'));
        slider2.attr('max', $(this).find(':selected').data('suma'));
    
    });
$('#wpr-calculator select').on('change',doAjax);
$('#wpr-calculator input').on('change',doAjax);




function doAjax(){
    var slider1 = document.getElementById("suma");
    var slider2 = document.getElementById("perioada");
    var output1 = document.getElementById("one");
    var output2 = document.getElementById("two");
    output1.innerHTML = slider1.value;
    output2.innerHTML = slider2.value;

    slider1.oninput = function() {
    output1.innerHTML = this.value;
    }
    slider2.oninput = function() {
        output2.innerHTML = this.value;
        }   
    var imprumuturi = $('#wpr-calculator select').val();
    var suma = $('#wpr-calculator #suma').val();
    var perioada = $('#wpr-calculator #perioada').val();
    

    data = {
        action: 'calculator',
        imprumuturi: imprumuturi,
        suma: suma,
        perioada: perioada,
    }
    // console.log(data);
    $.ajax({  
        url: WPR.ajax_url, 
        type: 'GET', 
        data: data,
        success: function(response){
            // console.log(response);
            
            if (response) {
                
                $('#wpr-calculator-results').empty();
                // console.log(response);
                var html = `
                <p class="loan_text">Rata lunara: ${response['rata'].toFixed(2)} lei</p>
                <p class="loan_text">Dobanda de plata: ${response['dobanda'].toFixed(2)} %/an</p>
                <p class="loan_text">Total de plata: ${response['total'].toFixed(2)} lei</p>
                `;
                 $('#wpr-calculator-results').append(html);
                
             }
        }
    })  
}
} ) (jQuery); 