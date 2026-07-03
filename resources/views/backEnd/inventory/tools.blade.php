@extends('backEnd.layouts.master')
@section('title', 'Inventory Tools')
@section('css')
<style>
/* Tool card shared */
.tool-card { border-radius: 12px; }
.tool-card .card-header { border-radius: 12px 12px 0 0; font-weight:600; }

/* Calculator */
#calc-display { font-size:1.6rem; text-align:right; padding:10px 14px; background:#1a1a2e; color:#00e5ff; border-radius:8px; min-height:56px; word-break:break-all; font-family:monospace; }
#calc-expr { font-size:0.75rem; color:#888; text-align:right; min-height:18px; }
.calc-btn { font-size:1rem; padding:10px 0; border-radius:8px; font-weight:600; }
.calc-btn.op { background:#e8f4ff; color:#0066cc; }
.calc-btn.fn { background:#fff3cd; color:#856404; }
.calc-btn.eq { background:#198754; color:#fff; }
.calc-btn.clr { background:#dc3545; color:#fff; }
.calc-btn.zero { grid-column: span 2; }

/* Unit converter */
.unit-result { font-size:1.4rem; font-weight:700; color:#198754; }

/* Profit calc */
.result-box { background:#f8f9fa; border-radius:8px; padding:14px; }
.result-box .label { font-size:0.8rem; color:#6c757d; }
.result-box .value { font-size:1.3rem; font-weight:700; }

/* Notepad */
#notepad-area { font-family: 'Courier New', monospace; font-size:0.9rem; resize:vertical; min-height:220px; }
#notepad-status { font-size:0.72rem; }

/* Discount calc */
.discount-result { font-size:1.1rem; font-weight:600; }
</style>
@endsection

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Inventory Tools</h4>
            </div>
        </div>
    </div>

    <div class="row g-3">

        {{-- ===== CALCULATOR ===== --}}
        <div class="col-lg-4 col-md-6">
            <div class="card tool-card h-100">
                <div class="card-header bg-dark text-white"><i class="fe-grid me-1"></i> Calculator</div>
                <div class="card-body">
                    <div id="calc-expr"></div>
                    <div id="calc-display">0</div>
                    <div class="mt-2" style="display:grid;grid-template-columns:repeat(4,1fr);gap:6px;">
                        <button class="btn calc-btn fn" onclick="calcFn('C')">C</button>
                        <button class="btn calc-btn fn" onclick="calcFn('±')">±</button>
                        <button class="btn calc-btn fn" onclick="calcFn('%')">%</button>
                        <button class="btn calc-btn op" onclick="calcFn('/')">÷</button>

                        <button class="btn calc-btn btn-light" onclick="calcNum('7')">7</button>
                        <button class="btn calc-btn btn-light" onclick="calcNum('8')">8</button>
                        <button class="btn calc-btn btn-light" onclick="calcNum('9')">9</button>
                        <button class="btn calc-btn op" onclick="calcFn('*')">×</button>

                        <button class="btn calc-btn btn-light" onclick="calcNum('4')">4</button>
                        <button class="btn calc-btn btn-light" onclick="calcNum('5')">5</button>
                        <button class="btn calc-btn btn-light" onclick="calcNum('6')">6</button>
                        <button class="btn calc-btn op" onclick="calcFn('-')">−</button>

                        <button class="btn calc-btn btn-light" onclick="calcNum('1')">1</button>
                        <button class="btn calc-btn btn-light" onclick="calcNum('2')">2</button>
                        <button class="btn calc-btn btn-light" onclick="calcNum('3')">3</button>
                        <button class="btn calc-btn op" onclick="calcFn('+')">+</button>

                        <button class="btn calc-btn btn-light zero" onclick="calcNum('0')">0</button>
                        <button class="btn calc-btn btn-light" onclick="calcNum('.')">.</button>
                        <button class="btn calc-btn eq" onclick="calcFn('=')">=</button>

                        <button class="btn calc-btn fn" onclick="calcFn('⌫')" style="grid-column:span 4; margin-top:4px;">⌫ Backspace</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== UNIT CONVERTER ===== --}}
        <div class="col-lg-4 col-md-6">
            <div class="card tool-card h-100">
                <div class="card-header bg-info text-white"><i class="fe-repeat me-1"></i> Unit Converter</div>
                <div class="card-body">
                    <ul class="nav nav-tabs mb-3" id="unit-tabs">
                        <li class="nav-item"><a class="nav-link active" href="#" data-tab="weight">Weight</a></li>
                        <li class="nav-item"><a class="nav-link" href="#" data-tab="length">Length</a></li>
                        <li class="nav-item"><a class="nav-link" href="#" data-tab="volume">Volume</a></li>
                        <li class="nav-item"><a class="nav-link" href="#" data-tab="area">Area</a></li>
                    </ul>
                    <div class="mb-3">
                        <input type="number" id="unit-input" class="form-control" placeholder="Enter value" step="any">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-5">
                            <select id="unit-from" class="form-select"></select>
                        </div>
                        <div class="col-2 text-center pt-2 fw-bold">→</div>
                        <div class="col-5">
                            <select id="unit-to" class="form-select"></select>
                        </div>
                    </div>
                    <div class="result-box text-center">
                        <div class="label">Result</div>
                        <div class="unit-result" id="unit-result">—</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== PROFIT MARGIN CALCULATOR ===== --}}
        <div class="col-lg-4 col-md-6">
            <div class="card tool-card h-100">
                <div class="card-header bg-success text-white"><i class="fe-trending-up me-1"></i> Profit Margin Calculator</div>
                <div class="card-body">
                    <ul class="nav nav-tabs mb-3" id="margin-tabs">
                        <li class="nav-item"><a class="nav-link active" href="#" data-margin-tab="forward">Cost → Price</a></li>
                        <li class="nav-item"><a class="nav-link" href="#" data-margin-tab="reverse">Margin → Price</a></li>
                    </ul>

                    <div id="margin-forward">
                        <div class="mb-2">
                            <label class="form-label small">Cost Price (৳)</label>
                            <input type="number" id="margin-cost" class="form-control" placeholder="e.g. 100" min="0" step="any">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Sale Price (৳)</label>
                            <input type="number" id="margin-sale" class="form-control" placeholder="e.g. 150" min="0" step="any">
                        </div>
                        <div class="row g-2">
                            <div class="col-4 result-box text-center">
                                <div class="label">Profit</div>
                                <div class="value text-success" id="res-profit">—</div>
                            </div>
                            <div class="col-4 result-box text-center">
                                <div class="label">Margin %</div>
                                <div class="value text-primary" id="res-margin">—</div>
                            </div>
                            <div class="col-4 result-box text-center">
                                <div class="label">Markup %</div>
                                <div class="value text-warning" id="res-markup">—</div>
                            </div>
                        </div>
                    </div>

                    <div id="margin-reverse" style="display:none;">
                        <div class="mb-2">
                            <label class="form-label small">Cost Price (৳)</label>
                            <input type="number" id="rev-cost" class="form-control" placeholder="e.g. 100" min="0" step="any">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Desired Margin %</label>
                            <input type="number" id="rev-margin" class="form-control" placeholder="e.g. 30" min="0" max="100" step="any">
                        </div>
                        <div class="result-box text-center">
                            <div class="label">Required Sale Price</div>
                            <div class="value text-success" id="rev-result">—</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== NOTEPAD ===== --}}
        <div class="col-lg-6 col-md-6">
            <div class="card tool-card h-100">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <span><i class="fe-file-text me-1"></i> Notepad</span>
                    <div class="d-flex gap-2 align-items-center">
                        <small id="notepad-status" class="text-muted"></small>
                        <button class="btn btn-sm btn-outline-dark py-0" onclick="clearNotepad()">Clear</button>
                    </div>
                </div>
                <div class="card-body p-2">
                    <textarea id="notepad-area" class="form-control border-0" placeholder="Type notes here… auto-saved to database.">{{ $noteContent }}</textarea>
                </div>
            </div>
        </div>

        {{-- ===== DISCOUNT CALCULATOR ===== --}}
        <div class="col-lg-6 col-md-6">
            <div class="card tool-card h-100">
                <div class="card-header bg-danger text-white"><i class="fe-tag me-1"></i> Discount Calculator</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label small">Original Price (৳)</label>
                            <input type="number" id="disc-price" class="form-control" placeholder="e.g. 500" min="0" step="any">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small">Discount %</label>
                            <input type="number" id="disc-pct" class="form-control" placeholder="e.g. 20" min="0" max="100" step="any">
                        </div>
                    </div>
                    <div class="row g-2 mt-3">
                        <div class="col-4 result-box text-center">
                            <div class="label">Discount Amount</div>
                            <div class="discount-result text-danger" id="disc-amount">—</div>
                        </div>
                        <div class="col-4 result-box text-center">
                            <div class="label">Final Price</div>
                            <div class="discount-result text-success" id="disc-final">—</div>
                        </div>
                        <div class="col-4 result-box text-center">
                            <div class="label">You Save</div>
                            <div class="discount-result text-primary" id="disc-save">—</div>
                        </div>
                    </div>

                    <hr>
                    <p class="fw-600 mb-2 small text-muted">Bulk Pricing</p>
                    <div class="row g-2">
                        <div class="col-sm-4">
                            <label class="form-label small">Unit Price (৳)</label>
                            <input type="number" id="bulk-unit" class="form-control" placeholder="100" min="0" step="any">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label small">Quantity</label>
                            <input type="number" id="bulk-qty" class="form-control" placeholder="10" min="1">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label small">Bulk Discount %</label>
                            <input type="number" id="bulk-disc" class="form-control" placeholder="5" min="0" max="100" step="any">
                        </div>
                    </div>
                    <div class="result-box text-center mt-2">
                        <div class="label">Total After Bulk Discount</div>
                        <div class="discount-result text-success" id="bulk-total">—</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('script')
<script>
// ===================== CALCULATOR =====================
var calcCurrent = '0', calcPrev = '', calcOp = null, calcNewNum = true;

function updateDisplay(){ document.getElementById('calc-display').textContent = calcCurrent; }
function updateExpr(t){ document.getElementById('calc-expr').textContent = t; }

function calcNum(n){
    if(calcNewNum){ calcCurrent = (n === '.') ? '0.' : n; calcNewNum = false; }
    else {
        if(n === '.' && calcCurrent.includes('.')) return;
        calcCurrent = (calcCurrent === '0' && n !== '.') ? n : calcCurrent + n;
    }
    updateDisplay();
}

function calcFn(fn){
    var v = parseFloat(calcCurrent);
    if(fn === 'C'){ calcCurrent='0'; calcPrev=''; calcOp=null; calcNewNum=true; updateDisplay(); updateExpr(''); return; }
    if(fn === '⌫'){
        calcCurrent = calcCurrent.length > 1 ? calcCurrent.slice(0,-1) : '0';
        updateDisplay(); return;
    }
    if(fn === '±'){ calcCurrent = String(v * -1); updateDisplay(); return; }
    if(fn === '%'){ calcCurrent = String(v / 100); updateDisplay(); return; }
    if(fn === '='){
        if(calcOp && calcPrev !== ''){
            var a = parseFloat(calcPrev), b = v, r;
            updateExpr(a + ' ' + calcOp + ' ' + b + ' =');
            switch(calcOp){
                case '+': r = a+b; break; case '-': r = a-b; break;
                case '*': r = a*b; break; case '/': r = b === 0 ? 'Error' : a/b; break;
            }
            calcCurrent = String(parseFloat(r.toFixed(10))); calcOp=null; calcPrev=''; calcNewNum=true; updateDisplay();
        }
        return;
    }
    // operator pressed
    if(calcOp && !calcNewNum){
        // chain
        var a2 = parseFloat(calcPrev), b2 = v, r2;
        switch(calcOp){
            case '+': r2=a2+b2; break; case '-': r2=a2-b2; break;
            case '*': r2=a2*b2; break; case '/': r2=b2===0?'Error':a2/b2; break;
        }
        calcCurrent = String(parseFloat(r2.toFixed(10))); updateDisplay();
    }
    calcPrev = calcCurrent; calcOp = fn; calcNewNum = true;
    updateExpr(calcPrev + ' ' + fn);
}

// Keyboard support
document.addEventListener('keydown', function(e){
    if('0123456789.'.includes(e.key)) calcNum(e.key);
    else if(['+','-','*','/'].includes(e.key)) calcFn(e.key);
    else if(e.key === 'Enter' || e.key === '=') calcFn('=');
    else if(e.key === 'Backspace') calcFn('⌫');
    else if(e.key.toLowerCase() === 'c') calcFn('C');
    else if(e.key === '%') calcFn('%');
});


// ===================== UNIT CONVERTER =====================
var unitDefs = {
    weight: {
        kg: 1, g: 1000, lb: 2.20462, oz: 35.274, mg: 1e6, ton: 0.001
    },
    length: {
        m: 1, cm: 100, mm: 1000, km: 0.001, inch: 39.3701, ft: 3.28084, yard: 1.09361, mile: 0.000621371
    },
    volume: {
        L: 1, mL: 1000, m3: 0.001, ft3: 0.0353147, gallon: 0.264172, cup: 4.22675, tbsp: 67.628, tsp: 202.884
    },
    area: {
        m2: 1, cm2: 1e4, km2: 1e-6, ft2: 10.7639, inch2: 1550, acre: 0.000247105, hectare: 1e-4
    }
};
var currentUnitTab = 'weight';

function populateUnits(tab){
    var units = Object.keys(unitDefs[tab]);
    var from = document.getElementById('unit-from');
    var to   = document.getElementById('unit-to');
    from.innerHTML = to.innerHTML = '';
    units.forEach(function(u, i){
        from.innerHTML += '<option value="'+u+'">'+u+'</option>';
        to.innerHTML   += '<option value="'+u+'"'+(i===1?' selected':'')+'>'+u+'</option>';
    });
    convertUnit();
}

function convertUnit(){
    var val  = parseFloat(document.getElementById('unit-input').value);
    var from = document.getElementById('unit-from').value;
    var to   = document.getElementById('unit-to').value;
    var tab  = currentUnitTab;
    var res  = document.getElementById('unit-result');
    if(isNaN(val)){ res.textContent='—'; return; }
    // convert to base then to target
    var base    = val / unitDefs[tab][from];
    var result  = base * unitDefs[tab][to];
    res.textContent = parseFloat(result.toFixed(8)) + ' ' + to;
}

document.querySelectorAll('#unit-tabs .nav-link').forEach(function(tab){
    tab.addEventListener('click', function(e){
        e.preventDefault();
        document.querySelectorAll('#unit-tabs .nav-link').forEach(function(t){ t.classList.remove('active'); });
        tab.classList.add('active');
        currentUnitTab = tab.dataset.tab;
        populateUnits(currentUnitTab);
    });
});
document.getElementById('unit-input').addEventListener('input', convertUnit);
document.getElementById('unit-from').addEventListener('change', convertUnit);
document.getElementById('unit-to').addEventListener('change', convertUnit);
populateUnits('weight');


// ===================== PROFIT MARGIN CALCULATOR =====================
document.querySelectorAll('#margin-tabs .nav-link').forEach(function(tab){
    tab.addEventListener('click', function(e){
        e.preventDefault();
        document.querySelectorAll('#margin-tabs .nav-link').forEach(function(t){ t.classList.remove('active'); });
        tab.classList.add('active');
        var mode = tab.dataset.marginTab;
        document.getElementById('margin-forward').style.display = mode==='forward' ? '' : 'none';
        document.getElementById('margin-reverse').style.display = mode==='reverse' ? '' : 'none';
    });
});

function calcMargin(){
    var cost = parseFloat(document.getElementById('margin-cost').value);
    var sale = parseFloat(document.getElementById('margin-sale').value);
    if(isNaN(cost)||isNaN(sale)||sale===0){ return; }
    var profit = sale - cost;
    var margin = (profit / sale) * 100;
    var markup = cost > 0 ? (profit / cost) * 100 : 0;
    document.getElementById('res-profit').textContent = '৳ '+profit.toFixed(2);
    document.getElementById('res-margin').textContent = margin.toFixed(2)+'%';
    document.getElementById('res-markup').textContent = markup.toFixed(2)+'%';
}

function calcReverse(){
    var cost   = parseFloat(document.getElementById('rev-cost').value);
    var margin = parseFloat(document.getElementById('rev-margin').value);
    if(isNaN(cost)||isNaN(margin)||margin>=100){ return; }
    var salePrice = cost / (1 - margin/100);
    document.getElementById('rev-result').textContent = '৳ '+salePrice.toFixed(2);
}

['margin-cost','margin-sale'].forEach(function(id){
    document.getElementById(id).addEventListener('input', calcMargin);
});
['rev-cost','rev-margin'].forEach(function(id){
    document.getElementById(id).addEventListener('input', calcReverse);
});


// ===================== NOTEPAD =====================
var notepadArea   = document.getElementById('notepad-area');
var notepadStatus = document.getElementById('notepad-status');
var notepadTimer;

notepadArea.addEventListener('input', function(){
    notepadStatus.textContent = 'Saving…';
    notepadStatus.className   = 'text-muted';
    clearTimeout(notepadTimer);
    notepadTimer = setTimeout(saveNotepad, 600);
});

function saveNotepad(){
    $.ajax({
        type: 'POST',
        url:  '{{ route('admin.inventory.notes.save') }}',
        data: { content: notepadArea.value, _token: '{{ csrf_token() }}' },
        success: function(){
            notepadStatus.textContent = '✓ Saved';
            notepadStatus.className   = 'text-success';
            setTimeout(function(){ notepadStatus.textContent=''; }, 2000);
        },
        error: function(){
            notepadStatus.textContent = 'Save failed';
            notepadStatus.className   = 'text-danger';
        }
    });
}

function clearNotepad(){
    if(!confirm('Clear notepad?')) return;
    notepadArea.value = '';
    saveNotepad();
}


// ===================== DISCOUNT CALCULATOR =====================
function calcDiscount(){
    var price = parseFloat(document.getElementById('disc-price').value);
    var pct   = parseFloat(document.getElementById('disc-pct').value);
    if(isNaN(price)||isNaN(pct)){ return; }
    var amount = price * pct / 100;
    var final  = price - amount;
    document.getElementById('disc-amount').textContent = '৳ '+amount.toFixed(2);
    document.getElementById('disc-final').textContent  = '৳ '+final.toFixed(2);
    document.getElementById('disc-save').textContent   = '৳ '+amount.toFixed(2);
}

function calcBulk(){
    var unit = parseFloat(document.getElementById('bulk-unit').value);
    var qty  = parseFloat(document.getElementById('bulk-qty').value);
    var disc = parseFloat(document.getElementById('bulk-disc').value) || 0;
    if(isNaN(unit)||isNaN(qty)){ return; }
    var subtotal = unit * qty;
    var total    = subtotal * (1 - disc/100);
    document.getElementById('bulk-total').textContent = '৳ '+total.toFixed(2)+' (save ৳'+(subtotal-total).toFixed(2)+')';
}

['disc-price','disc-pct'].forEach(function(id){ document.getElementById(id).addEventListener('input', calcDiscount); });
['bulk-unit','bulk-qty','bulk-disc'].forEach(function(id){ document.getElementById(id).addEventListener('input', calcBulk); });
</script>
@endsection
