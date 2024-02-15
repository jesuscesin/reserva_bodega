$(document).ready(onLoad);

function onLoad() {
    cargaMenu();
    $("#sidebar").on('click','a',cargaPage);
    $("#logout").click(logOut);
    $("#help").click(ventana);

}
function ventana() 
{ 
window.open("ayuda.php","Nueva ventana",'width=700,height=800'); 
} 
function cargaPage(e){
    e.preventDefault();
    var page = $(this).attr("href");
    //alert(page);
    if (page!="#") {
        $("div.main_new").empty().load(page);
    }
}
function logOut(e) {
    e.preventDefault();
    $.ajax({
        url:'principalx.php',
        type: 'GET',
        dataType: 'json',
        //contentType: false,
        data:{'logout':'logout'},
        //processData: false,
        //cache: false
        //beforeSend:cargando,
        success:function(jsonphp){
            if (jsonphp.LOGOUT) {
                // alert("Fin de sesión !");
				Swal.fire({
				    title: 'Cerrando Sesion',
					html: 'Por favor espere...',
					timerProgressBar: true,
					timer: 1500,
					didOpen: () => {
						Swal.showLoading()
						const b = Swal.getHtmlContainer().querySelector('b')
						timerInterval = setInterval(() => {
						b.textContent = Swal.getTimerLeft()
							}, 100)
						  },
						});
				setTimeout(() => { window.location.href = jsonphp.DOMINIO; }, 1500);

                
            }
        },
        //error:problemas
    });
}

function cargaMenu() {
    $.ajax({
        url:'principalx.php',
        type: 'GET',
        dataType: 'json',
        //contentType: false,
        data:{'cargaMenu':'cargaMenu'},
        //processData: false,
        //cache: false
        //beforeSend:cargando,
        success:function(jsonphp){

            if (jsonphp.NOTLOGIN) {
                //alert(jsonphp.DOMINIO);
                window.location.href = jsonphp.DOMINIO;
            }
            
            if (jsonphp.LOGIN) {

                var user='';
                var ul;
                
                $.each(jsonphp, function(indice, valores){
                    if($.isNumeric(indice)){
                        user=valores.USUARIO;
                        if (valores.NIVEL==1) {
                            ul = $("<ul/>").attr({'class':'nav nav-sidebar'});
                            var li = $("<li/>").attr({'class':'nav-header'});
							var i = $("<i/>").attr({'class':'nav-icon far fa-plus-square'});
                            var a = $("<a/>").attr({'href':valores.URL}).text(valores.NOMBRE);
                            ul.append(li.append(a));
                            ul.appendTo("#sidebar")
                        }else{
							var nom_menu = valores.NOMBRE.trim();
                             ul = $("<ul/>").attr({'class':'nav nav-sidebar nav-pills flex-column'});
                            var li = $("<li/>").attr({'class':'nav-item'});
							var i = $("<i/>").attr({'class':'far fa-circle nav-icon'}); 
							if(nom_menu=='Usuarios'){ var i = $("<i/>").attr({'class':'nav-icon far fa-user'}); }
							if(nom_menu=='Menu'){ var i = $("<i/>").attr({'class':'nav-icon fas fa-bars'}); }
							if(nom_menu=='Directorio Telefonico'){ var i = $("<i/>").attr({'class':'nav-icon fas fa-phone-alt'}); }
							if(nom_menu=='Planilla de Servidores'){ var i = $("<i/>").attr({'class':'nav-icon fas fa-server'}); }
							if(nom_menu=='Ordenes de Compra'){ var i = $("<i/>").attr({'class':'nav-icon fas fa-file-alt'}); }
							if(nom_menu=='Ordenes Emitidas'){ var i = $("<i/>").attr({'class':'nav-icon fas fa-file-export'}); }
							if(nom_menu=='MCH a COMEX'){ var i = $("<i/>").attr({'class':'nav-icon fas fa-upload'}); }
							if(nom_menu=='Procesos Monarch'){ var i = $("<i/>").attr({'class':'nav-icon fas fa-upload'}); }
							if(nom_menu=='Proceso Multivende'){ var i = $("<i/>").attr({'class':'nav-icon fas fa-upload'}); }
							if(nom_menu=='Token Multivende'){ var i = $("<i/>").attr({'class':'nav-icon fas fa-key'}); }
							if(nom_menu=='Planilla de BD'){ var i = $("<i/>").attr({'class':'nav-icon fas fa-database'}); }
							if(nom_menu=='Honorarios'){ var i = $("<i/>").attr({'class':'nav-icon fas fa-file-invoice'}); }
							if(nom_menu=='Devoluciones Scan'){ var i = $("<i/>").attr({'class':'nav-icon far fa-clipboard'}); }
							if(nom_menu=='Info Devoluciones'){ var i = $("<i/>").attr({'class':'nav-icon far fa-file-alt'}); }
							if(nom_menu=='Generar Transferencia'){ var i = $("<i/>").attr({'class':'nav-icon fas fa-exchange-alt'}); }
							if(nom_menu=='Valida Transferencia'){ var i = $("<i/>").attr({'class':'nav-icon fas fa-clipboard-check'}); }
							if(nom_menu=='Transferencias'){ var i = $("<i/>").attr({'class':'nav-icon fas fa-exchange-alt'}); }
							if(nom_menu=='Valida Transferencias'){ var i = $("<i/>").attr({'class':'nav-icon fas fa-clipboard-check'}); }
							if(nom_menu=='Homologar Artículos'){ var i = $("<i/>").attr({'class':'nav-icon fas fa-hand-holding-usd'}); } 
							if(nom_menu=='Proceso Planilla'){ var i = $("<i/>").attr({'class':'nav-icon fas fa-cloud-download-alt'}); } 
							if(nom_menu=='Articulos x Bodega'){ var i = $("<i/>").attr({'class':'nav-icon fas fa-list-alt'}); } 
							if(nom_menu=='Carga Equivalencias'){ var i = $("<i/>").attr({'class':'nav-icon fas fa-cloud-upload-alt'}); } 

                            var a = $("<a/>").attr({'class':'nav-link','href':valores.URL});
							var p = $("<p/>").text(valores.NOMBRE);
							// li.append(i);
                            ul.append(li.append(a.append(i).append(p)));
                            ul.appendTo("#sidebar")
                        }
                        
                    }   
                });
                $("#current-user").append(user);
                
            }
        },
        //error:problemas
    });
    
}

