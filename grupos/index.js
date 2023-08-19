$(document).ready(function (params) {
    $(".grupo-agregable").click(function(e) {
        const nrc = $(this).data('nrc')
        const tipoAdicion = $(this).data('tipo-adicion')
        entradaNrc = document.getElementById('nrc')
        entradaNrc.setAttribute('value', nrc)
        h1 = $('#textoDialogoAdicionGrupo')
    
        if (tipoAdicion == 'nuevo') {
            h1.text(' Agregar grupo de WhatsApp')
        } else if (tipoAdicion == 'actualizar') {
            h1.text(' Actualizar grupo de WhatsApp')
        }
    })

    $('#dialogoAdicionGrupo').submit(function(e) {
        e.preventDefault();
        // inputs = $('#' + $(this).attr('id') + ' input')
        inputNrc = document.getElementById('nrc')
        inputEnlace = document.getElementById('enlace')
        datos = {
            "nrc": inputNrc.value,
            "enlace": inputEnlace.value,
            "accion": 'agregarGrupo',
        }
        $.ajax({
            data: datos,
            method: 'POST',
        }).done(function(response) {
            console.log(response)
            if (response.exito) {
                $('#dialogoAdicionGrupo').modal('hide');
                location.reload()
            } else {
                alert('Error ' + response.codigo + ': ' + response.mensaje)
            }
        })
    })
})
