(function(){
    // Ajusta el margin-left de #content-wrapper según el ancho actual de la sidebar
    // Evita forzar un margin inline cuando la sidebar está explícitamente colapsada
    function adjustContentForSidebar(){
        var sidebar = document.querySelector('.sidebar');
        var content = document.getElementById('content-wrapper');
        if(!sidebar || !content) return;

        // En pantallas grandes, tomar el ancho real de la sidebar, excepto
        // cuando la sidebar esté colapsada (clase "toggled") o el body tenga
        // la clase `sidebar-toggled`. En esos casos dejamos que las reglas CSS
        // manejen el espacio (no aplicar margin inline).
        if(window.innerWidth >= 768){
            if (sidebar.classList.contains('toggled') || document.body.classList.contains('sidebar-toggled')) {
                content.style.marginLeft = '';
                return;
            }

            var w = Math.round(sidebar.getBoundingClientRect().width) || 0;
            // si el ancho es muy pequeño (0) dejamos que CSS controle el layout
            if (w <= 0) {
                content.style.marginLeft = '';
            } else {
                content.style.marginLeft = w + 'px';
            }
        } else {
            content.style.marginLeft = '';
        }
    }

    // Ejecutar en eventos relevantes
    window.addEventListener('resize', adjustContentForSidebar);
    document.addEventListener('DOMContentLoaded', adjustContentForSidebar);
    setTimeout(adjustContentForSidebar, 80);

    // Observar cambios de tamaño de la sidebar (cuando existan)
    try{
        var sidebarEl = document.querySelector('.sidebar');
        if(window.ResizeObserver && sidebarEl){
            var ro = new ResizeObserver(function(){ adjustContentForSidebar(); });
            ro.observe(sidebarEl);
        }
    }catch(e){ /* ignore */ }
})();