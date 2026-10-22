class administrarVistas{

    constructor(){
        this.enlaces = null;
        this.secciones = null;
        this.vista = null;

    }

    inicializar(){
        this.enlaces = document.querySelectorAll("a");
        this.enlaces = document.querySelectorAll("section");

        this.enlaces.forEach(e => {
            console.log(e.dataset.view);
            

            e.addEventListener("click", ()=>{
                console.log(e.dataset.view);
                //vista que queremos mostrar
                this.vista = e.dataset.view;
                //recorremos las secciones para mostrar u ocultar dependiendo de this vista
                this.secciones.foreach(s => {
                    //comprueba que vista esta seleccionada y la muestra y si no
                    //esta seleccionada la oculta utilizando el hidden.
                    if(s.dataset.view == this.vista){
                        s.hidden = false;
                    }else{
                        s.hidden = true;
                    }


                    console.log(e.dataset.view);


                });

            });
            
        });



    }


}
//creamos el objeto
let admin = new administrarVistas();
//llamamos a la funcion
admin.inicializar();