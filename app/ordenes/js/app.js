
function LoadOrdenes(){
    var parametros = $("#FormQueryUnidades").serialize();
    $("#LoadOrdenes").fadeIn('slow');
    $.ajax({
        type:'POST',
        url:'modal/TablaOperaciones.php',
        data: parametros,
        beforeSend: function(objeto){
            $("#LoadOrdenes").html("<center><img src='../image/loading-46.gif'></center>");
        },
        success:function(data){
            $("#LoadOrdenes").html(data);
        }
    });
}

function LoadDatosOps(){
    var parametros = $("#FormQueryUnidades").serialize();
    $("#LoadDatosOps").fadeIn('slow');
    $.ajax({
        type:'POST',
        url:'modal/LoadDatosOps.php',
        data: parametros,
        beforeSend: function(objeto){
            $("#LoadDatosOps").html("");
        },
        success:function(data){
            $("#LoadDatosOps").html(data);
        }
    });
}

function LoadAreas(){
    var parametros = $("#FormQueryUnidades").serialize();
    $("#LoadAreas").fadeIn('slow');
    $.ajax({
        type:'POST',
        url:'modal/LoadAreas.php',
        data: parametros,
        beforeSend: function(objeto){
            $("#LoadAreas").html("");
        },
        success:function(data){
            $("#LoadAreas").html(data);
        }
    });
}

function LoadCopes(){
    var parametros = $("#FormQueryUnidades").serialize();
    $("#LoadCopes").fadeIn('slow');
    $.ajax({
        type:'POST',
        url:'modal/LoadCopes.php',
        data: parametros,
        beforeSend: function(objeto){
            $("#LoadCopes").html("");
        },
        success:function(data){
            $("#LoadCopes").html(data);
        }
    });
}

function LoadOrdenesIncompletas(){
    var parametros = $("#FormQueryUnidades").serialize();
    $("#LoadOrdenesIncompletas").fadeIn('slow');
    $.ajax({
        type:'POST',
        url:'modal/TablaIncompletas.php',
        data: parametros,
        beforeSend: function(objeto){
            $("#LoadOrdenesIncompletas").html("<center><img src='../image/loading-46.gif'></center>");
        },
        success:function(data){
            $("#LoadOrdenesIncompletas").html(data);
        }
    });
}

function LoadOrdenesTac(){
    var parametros = $("#FormQueryUnidades").serialize();
    $("#LoadOrdenesTac").fadeIn('slow');
    $.ajax({
        type:'POST',
        url:'modal/TablaTac.php',
        data: parametros,
        beforeSend: function(objeto){
            $("#LoadOrdenesTac").html("<center><img src='../image/loading-46.gif'></center>");
        },
        success:function(data){
            $("#LoadOrdenesTac").html(data);
        }
    });
}

function LoadKPIS(){
    var parametros = $("#FormQueryUnidades").serialize();
    $("#LoadKPIS").fadeIn('slow');
    // Show spinner
    $("#LoadKPIS").html("<center><img src='../image/loading-46.gif'></center>");

    // Abort any previous KPI requests to avoid piling up
    try{
        if(!window.kpiRequests) window.kpiRequests = [];
        window.kpiRequests.forEach(function(r){ try{ r && r.abort && r.abort(); }catch(e){} });
    }catch(e){ }

    // Helper: client-side JSON flow (meta + coordi + TAC parts)
    function clientFlow(parametros){
        var reqMeta = $.ajax({ type:'POST', url:'modal/indicadores_cabezaras.php', data: parametros, dataType: 'json', timeout: 15000 });
        var reqTacDiv = $.ajax({ type:'POST', url:'modal/indicadores_tac.php', data: parametros + '&part=divisiones', dataType: 'json', timeout: 15000 });
        var reqTacAreas = $.ajax({ type:'POST', url:'modal/indicadores_tac.php', data: parametros + '&part=areas', dataType: 'json', timeout: 15000 });
        var reqTacCopes = $.ajax({ type:'POST', url:'modal/indicadores_tac.php', data: parametros + '&part=copes', dataType: 'json', timeout: 15000 });
        var reqCoordi = $.ajax({ type:'POST', url:'modal/indicadores_coordiapp.php', data: parametros, dataType: 'json', timeout: 15000 });
        window.kpiRequests = [reqMeta, reqTacDiv, reqTacAreas, reqTacCopes, reqCoordi];

        // Console logs for debugging/visibility
        reqMeta.done(function(res){ console.log('LoadKPIS: indicadores_cabezaras loaded', res); }).fail(function(xhr, status, err){ console.warn('LoadKPIS: indicadores_cabezaras failed', status, err); }).always(function(){ try{ console.log('LoadKPIS: indicadores_cabezaras complete'); }catch(e){} });
        reqTacDiv.done(function(res){ console.log('LoadKPIS: indicadores_tac(divisiones) loaded', res); }).fail(function(xhr, status, err){ console.warn('LoadKPIS: indicadores_tac(divisiones) failed', status, err); }).always(function(){ try{ console.log('LoadKPIS: indicadores_tac(divisiones) complete'); }catch(e){} });
        reqTacAreas.done(function(res){ console.log('LoadKPIS: indicadores_tac(areas) loaded', res); }).fail(function(xhr, status, err){ console.warn('LoadKPIS: indicadores_tac(areas) failed', status, err); }).always(function(){ try{ console.log('LoadKPIS: indicadores_tac(areas) complete'); }catch(e){} });
        reqTacCopes.done(function(res){ console.log('LoadKPIS: indicadores_tac(copes) loaded', res); }).fail(function(xhr, status, err){ console.warn('LoadKPIS: indicadores_tac(copes) failed', status, err); }).always(function(){ try{ console.log('LoadKPIS: indicadores_tac(copes) complete'); }catch(e){} });
        reqCoordi.done(function(res){ console.log('LoadKPIS: indicadores_coordiapp loaded', res); }).fail(function(xhr, status, err){ console.warn('LoadKPIS: indicadores_coordiapp failed', status, err); }).always(function(){ try{ console.log('LoadKPIS: indicadores_coordiapp complete'); }catch(e){} });

        Promise.allSettled([reqMeta, reqTacDiv, reqTacAreas, reqTacCopes, reqCoordi]).then(function(results){
            console.log('LoadKPIS: allSettled results', results);
            var combined = { ok: true, meta: {}, divisionesTac: [], divisionesCoordi: [], areasTac: [], areasCoordi: [], copesTac: [], copesCoordi: [], tecnicos: [], failed: { tac: false, coordi: false, meta: false } };
            var anySuccess = false;

            if(results[0] && results[0].status === 'fulfilled'){
                anySuccess = true;
                try{ combined.meta = (results[0].value && results[0].value.meta) ? results[0].value.meta : combined.meta; }catch(e){}
                combined.failed.meta = false;
            } else { combined.failed.meta = true; }

            var tacAny = false;
            if(results[1] && results[1].status === 'fulfilled'){ tacAny = true; anySuccess = true; try{ combined.divisionesTac = (results[1].value && results[1].value.divisionesTac) ? results[1].value.divisionesTac : combined.divisionesTac; }catch(e){} }
            if(results[2] && results[2].status === 'fulfilled'){ tacAny = true; anySuccess = true; try{ combined.areasTac = (results[2].value && results[2].value.areasTac) ? results[2].value.areasTac : combined.areasTac; }catch(e){} }
            if(results[3] && results[3].status === 'fulfilled'){ tacAny = true; anySuccess = true; try{ combined.copesTac = (results[3].value && results[3].value.copesTac) ? results[3].value.copesTac : combined.copesTac; }catch(e){} }
            combined.failed.tac = !tacAny;

            if(results[4] && results[4].status === 'fulfilled'){
                anySuccess = true;
                try{
                    combined.divisionesCoordi = (results[4].value && results[4].value.divisionesCoordi) ? results[4].value.divisionesCoordi : combined.divisionesCoordi;
                    combined.areasCoordi = (results[4].value && results[4].value.areasCoordi) ? results[4].value.areasCoordi : combined.areasCoordi;
                    combined.copesCoordi = (results[4].value && results[4].value.copesCoordi) ? results[4].value.copesCoordi : combined.copesCoordi;
                    combined.tecnicos = (results[4].value && results[4].value.tecnicos) ? results[4].value.tecnicos : combined.tecnicos;
                }catch(e){}
                combined.failed.coordi = false;
            } else { combined.failed.coordi = true; }

            if(!anySuccess){
                // Try server-assembled kpis2.php as a graceful fallback before showing an error
                $.ajax({ type:'POST', url:'modal/kpis2.php', data:parametros, dataType:'html', timeout:15000, success:function(html){ $('#LoadKPIS').html(html); try{ if(window.initKpiCharts) window.initKpiCharts(true); }catch(e){} }, error: function(){ $('#LoadKPIS').html('<div class="alert alert-warning">No se pudo cargar KPIs (intente recargar)</div>'); } });
                return;
            }

            try{
                if(typeof renderKpisFromJson !== 'function'){
                    $.getScript('js/kpis_renderer.js').done(function(){ window.renderKpisFromJson('#LoadKPIS', combined); }).fail(function(){ $('#LoadKPIS').html('<div class="alert alert-warning">No se pudo cargar el renderer de KPIs. Intente recargar.</div>'); });
                } else {
                    window.renderKpisFromJson('#LoadKPIS', combined);
                }
            }catch(e){ console.warn('render error', e); $('#LoadKPIS').html('<div class="alert alert-warning">Error rendering KPIs</div>'); }

        }).catch(function(e){
            console.warn('LoadKPIS combine error', e);
            $.ajax({ type:'POST', url:'modal/kpis2.php', data:parametros, dataType:'html', success:function(html){ $('#LoadKPIS').html(html); try{ if(window.initKpiCharts) window.initKpiCharts(true); }catch(e){} }, error: function(){ $('#LoadKPIS').html('<div class="alert alert-warning">No se pudo cargar KPIs (intente recargar)</div>'); } });
        });
    }

    // Run the normal client JSON flow (meta + coordi + TAC parts)
    clientFlow(parametros);

    // Console logs for debugging/visibility: each request reports success/failure
    reqMeta.done(function(res){ console.log('LoadKPIS: indicadores_cabezaras loaded', res); }).fail(function(xhr, status, err){ console.warn('LoadKPIS: indicadores_cabezaras failed', status, err); }).always(function(){ try{ console.log('LoadKPIS: indicadores_cabezaras complete'); }catch(e){} });
    reqTacDiv.done(function(res){ console.log('LoadKPIS: indicadores_tac(divisiones) loaded', res); }).fail(function(xhr, status, err){ console.warn('LoadKPIS: indicadores_tac(divisiones) failed', status, err); }).always(function(){ try{ console.log('LoadKPIS: indicadores_tac(divisiones) complete'); }catch(e){} });
    reqTacAreas.done(function(res){ console.log('LoadKPIS: indicadores_tac(areas) loaded', res); }).fail(function(xhr, status, err){ console.warn('LoadKPIS: indicadores_tac(areas) failed', status, err); }).always(function(){ try{ console.log('LoadKPIS: indicadores_tac(areas) complete'); }catch(e){} });
    reqTacCopes.done(function(res){ console.log('LoadKPIS: indicadores_tac(copes) loaded', res); }).fail(function(xhr, status, err){ console.warn('LoadKPIS: indicadores_tac(copes) failed', status, err); }).always(function(){ try{ console.log('LoadKPIS: indicadores_tac(copes) complete'); }catch(e){} });
    reqCoordi.done(function(res){ console.log('LoadKPIS: indicadores_coordiapp loaded', res); }).fail(function(xhr, status, err){ console.warn('LoadKPIS: indicadores_coordiapp failed', status, err); }).always(function(){ try{ console.log('LoadKPIS: indicadores_coordiapp complete'); }catch(e){} });

    // Combine results (use Promise.allSettled for partial success handling)
    Promise.allSettled([reqMeta, reqTacDiv, reqTacAreas, reqTacCopes, reqCoordi]).then(function(results){
        console.log('LoadKPIS: allSettled results', results);
        // Build combined data object expected by kpis_renderer.js
        var combined = { ok: true, meta: {}, divisionesTac: [], divisionesCoordi: [], areasTac: [], areasCoordi: [], copesTac: [], copesCoordi: [], tecnicos: [], failed: { tac: false, coordi: false, meta: false } };
        var anySuccess = false;

        // results[0] -> meta, results[1] -> tacDiv, results[2] -> tacAreas, results[3] -> tacCopes, results[4] -> coordi
        // Treat a request as successful if the promise fulfilled (regardless of an `ok` flag)
        // We want to render any data we receive and only fallback when ALL requests fail/reject.
        if(results[0] && results[0].status === 'fulfilled'){
            anySuccess = true;
            try{ combined.meta = (results[0].value && results[0].value.meta) ? results[0].value.meta : combined.meta; }catch(e){}
            combined.failed.meta = false;
        } else {
            combined.failed.meta = true;
        }

        // TAC parts: treat each subrequest separately; if at least one succeeds, consider TAC partially available
        var tacAny = false;
        if(results[1] && results[1].status === 'fulfilled'){
            tacAny = true; anySuccess = true;
            try{ combined.divisionesTac = (results[1].value && results[1].value.divisionesTac) ? results[1].value.divisionesTac : combined.divisionesTac; }catch(e){}
        }
        if(results[2] && results[2].status === 'fulfilled'){
            tacAny = true; anySuccess = true;
            try{ combined.areasTac = (results[2].value && results[2].value.areasTac) ? results[2].value.areasTac : combined.areasTac; }catch(e){}
        }
        if(results[3] && results[3].status === 'fulfilled'){
            tacAny = true; anySuccess = true;
            try{ combined.copesTac = (results[3].value && results[3].value.copesTac) ? results[3].value.copesTac : combined.copesTac; }catch(e){}
        }
        combined.failed.tac = !tacAny;

        if(results[4] && results[4].status === 'fulfilled'){
            anySuccess = true;
            try{
                combined.divisionesCoordi = (results[4].value && results[4].value.divisionesCoordi) ? results[4].value.divisionesCoordi : combined.divisionesCoordi;
                combined.areasCoordi = (results[4].value && results[4].value.areasCoordi) ? results[4].value.areasCoordi : combined.areasCoordi;
                combined.copesCoordi = (results[4].value && results[4].value.copesCoordi) ? results[4].value.copesCoordi : combined.copesCoordi;
                combined.tecnicos = (results[4].value && results[4].value.tecnicos) ? results[4].value.tecnicos : combined.tecnicos;
            }catch(e){}
            combined.failed.coordi = false;
        } else {
            combined.failed.coordi = true;
        }

        if(!anySuccess){
            // If none succeeded, fallback to legacy full HTML endpoint
            $.ajax({ type:'POST', url:'modal/KPIS.php', data:parametros, success:function(html){ $('#LoadKPIS').html(html); $('#LoadKPIS').find('script').each(function(){ try{ $.globalEval(this.text || this.textContent || this.innerHTML || ''); }catch(e){} }); if(window.initKpiCharts) try{ window.initKpiCharts(true); }catch(e){} } , error: function(){ $('#LoadKPIS').html('<div class="alert alert-danger">Error cargando KPIs</div>'); } });
            return;
        }

        // Ensure renderer is loaded and render with combined data
        try{
            if(typeof renderKpisFromJson !== 'function'){
                $.getScript('js/kpis_renderer.js').done(function(){ window.renderKpisFromJson('#LoadKPIS', combined); }).fail(function(){ $('#LoadKPIS').html('<div class="alert alert-danger">Error cargando renderer</div>'); });
            } else {
                window.renderKpisFromJson('#LoadKPIS', combined);
            }
        }catch(e){ console.warn('render error', e); $('#LoadKPIS').html('<div class="alert alert-danger">Error rendering KPIs</div>'); }

    }).catch(function(e){
        console.warn('LoadKPIS combine error', e);
        // final fallback
        $.ajax({ type:'POST', url:'modal/KPIS.php', data:parametros, success:function(html){ $('#LoadKPIS').html(html); $('#LoadKPIS').find('script').each(function(){ try{ $.globalEval(this.text || this.textContent || this.innerHTML || ''); }catch(e){} }); if(window.initKpiCharts) try{ window.initKpiCharts(true); }catch(e){} } , error: function(){ $('#LoadKPIS').html('<div class="alert alert-danger">Error cargando KPIs</div>'); } });
    });
}

function LoadAlerts(){
    var parametros = $("#FormQueryUnidades").serialize();
    $("#LoadAlerts").fadeIn('slow');
    $.ajax({
        type:'POST',
        url:'modal/TablaAlerts.php',
        data: parametros,
        beforeSend: function(objeto){
            $("#LoadAlerts").html("<center><img src='../image/loading-46.gif'></center>");
        },
        success:function(data){
            $("#LoadAlerts").html(data);
        }
    });
}