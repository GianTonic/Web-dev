<html lang="en">
<style> </style>
<head></head>
<body>
<div style="overflow-x:auto;">
    <table id="tableProdotti" class="table">
            <thead>
              <tr>
                <th scope="col">Nome Prodotto</th>
                <th scope="col">Venduto settimana precedente</th>
                <th scope="col">Stock</th>
                <th scope="col">Spazio Cella</th>
                <th scope="col">Giacenza</th>
                <th scope="col">Produzione settimana in corso</th>
                <th scope="col">Giacenza Futura</th>
                <th scope="col">Produzione Futura</th>
              </tr>
            </thead>
            <tfoot>
              <tr>
                <th scope="col">Nome Prodotto</th>
                <th scope="col">Venduto settimana precedente</th>
                <th scope="col">Stock</th>
                <th scope="col">Spazio Cella</th>
                <th scope="col">Giacenza</th>
                <th scope="col">Produzione settimana in corso</th>
                <th scope="col">Giacenza Futura</th>
                <th scope="col">Produzione Futura</th>
              </tr>
            </tfoot>
          
    </table>
</div>
</body>          
</html>

<script>
var table;
$(document).ready(function() {

  const createdCell = function(cell) {
	var original;
  cell.setAttribute('contenteditable', true)
  cell.setAttribute('spellcheck', false)
  cell.addEventListener("focus", function(e) {
		original = e.target.textContent;
    e.target.textContent = '';
	})
  cell.addEventListener("blur", function(e) {
  //VALORE CORRENTE CELLA MODIFICATA e.target.textContent
		if (original !== e.target.textContent && e.target.textContent!=='') {
    //INDEXs CELLA CORRENTE
      var irow = table.cell(this).index().row;
      var icolumn = table.cell(this).index().columnVisible;
    //MODIFICA definitivamente CELLA con nuovo valore immesso
      table.cell(irow,icolumn).data(e.target.textContent);
    //GET VALORI CELLE
      var vsp = table.cell(irow,1).data();
      var stock = table.cell(irow,2).data();
      var sc = table.cell(irow,3).data();
      var g = table.cell(irow,4).data();
      var psic = table.cell(irow,5).data();
      var gf = table.cell(irow,6).data();
    //NOME COLONNA DELLA CELLA CLICCATA
      var nomeColonna = $('#tableProdotti th').eq($(this).index()).text();
      switch(nomeColonna){
        case 'Venduto settimana precedente':
          sc = calcolaSpazioCella(vsp,stock);
          table.cell(irow,3).data(sc);
          gf = calcolaGiacenzaFutura(psic,g,vsp,sc);
          table.cell(irow,6).data(gf);
          pf = calcolaProduzioneFutura(vsp,sc,gf);
          table.cell(irow,7).data(pf);
          break;
        case 'Stock':
          sc = calcolaSpazioCella(vsp,stock,gf);
          table.cell(irow,3).data(sc);
          pf = calcolaProduzioneFutura(vsp,sc,gf);
          table.cell(irow,7).data(pf);
          break;
        case 'Giacenza':
          gf = calcolaGiacenzaFutura(psic,g,vsp,sc);
          table.cell(irow,6).data(gf);
          pf = calcolaProduzioneFutura(vsp,sc,gf);
          table.cell(irow,7).data(pf);
          break;  
        case 'Produzione settimana in corso':
          gf = calcolaGiacenzaFutura(psic,g,vsp,sc);
          table.cell(irow,6).data(gf);
          pf = calcolaProduzioneFutura(vsp,sc,gf);
          table.cell(irow,7).data(pf);
          break;
        default:
          console.log('Sorry, we are out of ${nomeColonna}.');
      }
    }
    else{
      const row = table.row(e.target.parentElement)
      row.invalidate()
    }
  })
} 
  table = $('#tableProdotti').DataTable( {
        ajax: "prodotti.json",
        dom: 'Bfrtip',
        buttons: [
          {
                text: 'Delete',
                action: function ( e, dt, node, config ) {
                    alert( 'Button activated' );
                }
          },'csv', 'excel', 'pdf'
        ],
        columnDefs: [
          { 
            targets: [1,2,4,5,7], //'_all'
            createdCell: createdCell
          }
        ]
      } );
} );

function calcolaSpazioCella(vsp,stock) {
  //alert(vsp+' '+stock)
  let mvsp = parseFloat(vsp)*parseFloat(1.7);
  let sc =  parseFloat(mvsp)+parseFloat(stock);
  return sc;
}

function calcolaGiacenzaFutura(psic,g,vsp,sc) {
  let gf = parseFloat(psic)+parseFloat(g)-parseFloat(vsp);
  //alert(gf+' = '+psic+' '+g+' '+vsp);
  return gf; 
}

function calcolaProduzioneFutura(vsp,sc,gf) {
  //alert('')
  let pf = parseFloat(0);
  if((parseFloat(gf)/parseFloat(vsp))>1.5){
    //alert('gf '+parseFloat(gf)+'/ vsp '+parseFloat(vsp)+' = '+(parseFloat(gf)/parseFloat(vsp)));
    return pf;
  }
  else{
    //alert('gf '+parseFloat(gf)+'/ vsp '+parseFloat(vsp)+' = '+(parseFloat(gf)/parseFloat(vsp)));
    pf=parseFloat(sc)-parseFloat(gf);
    //alert('sc '+parseFloat(sc)+'- gf '+parseFloat(gf)+' = pf '+pf);
    return pf;
  }
}

</script>
