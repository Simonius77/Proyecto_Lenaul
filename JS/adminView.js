class administrarVistas{

    constructor(){
        this.enlaces = null;
        this.secciones = null;
        this.vista = null;
        this.productos = r;
        this.categorias = null;
        this.platos = null;
        this.usuarios = null;

    }

    inicializar(){
        //enlaza con la api 
        fetch("http://localhost/Proyecto_Lenaul/?controller=producto&action=api")
        //optiene transforma los datos a json
        .then(respuesta => respuesta.json())
        .then(r =>  {
            this.productos = r;
            const tablaproductos = document.querySelector("#productos");
            this.productos.forEach(p => {
                const tr = document.createElement("tr");
                const td1 = document.createElement("td");
                td1.textContent = p.id;
                tr.append(td1);
                const td2 = document.createElement("td");
                td2.textContent = p.nombre;
                tr.append(td2);
                const td3 = document.createElement("td");
                td3.textContent = p.precio;
                tr.append(td3);
                
                
                tablaproductos.append(tr);
            })     
                
        })
        
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

class platos {
    constructor(){
        this.id = id;
        this.nombre = nombre;
        this.precio = precio;

    }
}

const formularioEliminarProducto = document.querySelector("#formularioEliminarProducto");
formularioEliminarProducto.addEventListener("submit", (e) => {
    e.preventDefault();
    console.log(e.target[0].value);
    fetch("http://localhost/Proyecto_Lenaul/?controller=producto&action=api",{
        method: "DELETE",
        body: JSON.stringify(id)
        
})

    
        

})