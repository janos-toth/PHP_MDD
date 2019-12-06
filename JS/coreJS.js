$(window).load(function(){

    $("tr[id^='subQst_']").on('click',function(){
        $("tr." + $(this).attr('id') + "_subCateg").toggle();
        $(this).find("td:eq(0)>span").toggleClass( "caretDown" );
        $(this).find("td:eq(0)>span").toggleClass( "caretUp" );
    });
    $("tr[class$='subCateg']").css("background-color","#f5f5f5");

    $("tr[id^='innerQst_']").on('click',function(){
        $("tr." + $(this).attr('id') + "_subCateg").toggle();

        $(this).find("td:eq(0)>span").toggleClass( "caretDown" );
        $(this).find("td:eq(0)>span").toggleClass( "caretUp" );
    });
//---------------------------------------------------------------------------------------
    var btn = $('#topButton');

    $(window).scroll(function() {
    if ($(window).scrollTop() > 300) {
        btn.addClass('show');
    } else {
        btn.removeClass('show');
    }
    });
    btn.on('click', function(e) {
    e.preventDefault();
    $('html, body').animate({scrollTop:0}, '300');
    });
//---------------------------------------------------------------------------------------
    if(window.location.href.search("project") >= 0){
        $(".jumbotron").show();
        $(".container>h3").show();
    }
    $("tbody").show();
    $(".container").show();
//---------------------------------------------------------------------------------------
    $('.GetProject').click(function(e){
        e.preventDefault();

        $("tbody").hide();
        $(".jumbotron").hide();
        $(".container>h3").hide();
        $("#plsWait").show();
        $("#getProjForm").submit();

    })
//---------------------------------------------------------------------------------------
    $(".nav li a").on('click',function(e){
        e.preventDefault();
        var lang = $(this).text();

        $("tbody").hide();
        $(".jumbotron").hide();
        $(".container>h3").hide();
        $("#plsWait").show();

        var Url = window.location.href + '&lang=' +  lang; 
        if(getQueryParam("lang")){
            Url = window.location.search.replace(/&lang=\w{3}/g,"&lang="+lang);
          }else {
            Url = window.location.href + '&lang=' +  lang; 
          }
        window.location.assign(Url);

    });

//---------------------------------------------------------------------------------------
//fileSaving
    var wb = XLSX.utils.table_to_book(document.getElementById('tableToDownload'), {sheet:"Sheet JS"});
    var wbout = XLSX.write(wb, {bookType:'xlsx', bookSST:true, type: 'binary'});
    function s2ab(s) {
        var buf = new ArrayBuffer(s.length);
        var view = new Uint8Array(buf);
        for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
        return buf;
    }
    $("#button-a").click(function(e){
        e.preventDefault();
        saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), 'questionnaire.xlsx');
    });
})
//---------------------------------------------------------------------------------------
function getQueryParam(param) {
    var result =  window.location.search.match(
        new RegExp("(\\?|&)" + param + "(\\[\\])?=([^&]*)")
    );
    return result ? result[3] : "";
}