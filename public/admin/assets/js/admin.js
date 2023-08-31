$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$(document).ready(function(){
    //select2
    if( $('.select2').length > 0 ){
        $('.select2').select2();
    }
    //privileges page
    $('#is_super_admin').change(function (){
        if( $(this).val() == '1' ){
            $('#module_permission_section').hide();
        } else {
            $('#module_permission_section').show();
        }
    })
    //permission page checkbox
    $('.check_all').click( function(e){
        let checkbox_type = $(this).val();
        if( $(this).is(':checked') ){
            $('.'+ checkbox_type +'_checkbox').prop('checked',true);
        }else{
            $('.'+ checkbox_type +'_checkbox').prop('checked',false);
        }
    })
    //menu active class
    let url_module = current_url.split('/')[4];
    $('.nav-item').each( function(){
        let menu_link = $(this).find('.nav-link').attr('href');
        menu_link = menu_link.split('/');
        if( menu_link.indexOf(url_module) != -1){
            $(this).addClass('active');
        }
    })
})
function ajaxRequest( url, method, params = {} )
{
    return new Promise( (resolve,reject) => {
        $.ajax({
            type:method,
            url:url,
            data:params,
            beforeSend:function(){
                $('#overlay').show();
            },
            success:function(data){
                $('#overlay').hide();
                resolve(data);
            }
        })
    })
}
