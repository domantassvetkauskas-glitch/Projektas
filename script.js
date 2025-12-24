// aktyvi nuoroda pagal scroll (scrollspy)
(function(){
  const links = [...document.querySelectorAll('.mainnav a')];
  const sections = links.map(a => document.querySelector(a.getAttribute('href'))).filter(Boolean);

  function setActiveByHash(hash){
    links.forEach(a => a.classList.toggle('active', a.getAttribute('href') === hash));
  }

  function onScroll(){
    const y = window.scrollY + 120; // šiek tiek žemiau viršaus
    let current = sections[0];
    for(const s of sections){
      if(s.offsetTop <= y) current = s;
    }
    if(current){ setActiveByHash('#'+current.id); }
  }

  links.forEach(a => a.addEventListener('click', e=>{
    setActiveByHash(a.getAttribute('href'));
  }));
  window.addEventListener('scroll', onScroll, {passive:true});
  onScroll();
})();

// mobilus meniu
(function(){
  const btn = document.querySelector('.menu-toggle');
  const nav = document.getElementById('mainnav');
  if(!btn || !nav) return;
  btn.addEventListener('click', ()=>{
    const open = nav.classList.toggle('open');
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
  });
})();

// metai poraštėje
document.getElementById('y') && (document.getElementById('y').textContent = new Date().getFullYear());

// krepšelis
const cart = [];
const cartBtn = document.getElementById('cartBtn');
const cartDrawer = document.getElementById('cartDrawer');
const cartItemsEl = document.getElementById('cartItems');
const cartTotalEl = document.getElementById('cartTotal');
const scrim = document.querySelector('.drawer-scrim');

function fmt(n){ return n.toFixed(2).replace('.', ',') + ' €'; }
function openDrawer(el){ el.classList.add('open'); scrim.hidden = false; }
function closeDrawers(){ document.querySelectorAll('.drawer').forEach(d=>d.classList.remove('open')); scrim.hidden = true; }

function renderCart(){
  if(!cartItemsEl) return;
  if(cart.length===0){
    cartItemsEl.innerHTML = '<p class="muted">Krepšelis tuščias.</p>';
    cartTotalEl.textContent = fmt(0);
    return;
  }
  cartItemsEl.innerHTML = cart.map((it,i)=>`
    <div class="d-flex align-items-center gap-2 mb-3">
      <img src="${it.img}" alt="" style="width:56px;height:auto;border-radius:8px;border:1px solid #eee">
      <div class="flex-grow-1">
        <div class="fw-semibold">${it.name}</div>
        <div class="small text-muted">${fmt(it.price)} × ${it.qty}</div>
      </div>
      <button class="btn btn-sm btn-outline-secondary" data-remove="${i}">–</button>
      <button class="btn btn-sm btn-outline-secondary" data-add="${i}">+</button>
    </div>
  `).join('');
  const total = cart.reduce((s, it)=> s + it.price*it.qty, 0);
  cartTotalEl.textContent = fmt(total);
}

document.addEventListener('click', (e)=>{
  const addBtn = e.target.closest('.add-to-cart');
  if(addBtn){
    const id = addBtn.dataset.id;
    const found = cart.find(i=>i.id===id);
    if(found){ found.qty++; }
    else{
      cart.push({
        id,
        name: addBtn.dataset.name,
        price: parseFloat(addBtn.dataset.price || '0'),
        img: addBtn.dataset.img || '',
        qty: 1
      });
    }
    renderCart();
    openDrawer(cartDrawer);
  }

  if(e.target.matches('[data-remove]')){
    const i = +e.target.dataset.remove;
    if(cart[i]){ cart[i].qty--; if(cart[i].qty<=0) cart.splice(i,1); }
    renderCart();
  }
  if(e.target.matches('[data-add]')){
    const i = +e.target.dataset.add;
    if(cart[i]){ cart[i].qty++; }
    renderCart();
  }

  if(e.target.closest('.drawer-close')) closeDrawers();
});

cartBtn?.addEventListener('click', ()=>{ renderCart(); openDrawer(cartDrawer); });
scrim?.addEventListener('click', closeDrawers);

// demonstracinis „checkout“
document.getElementById('checkoutBtn')?.addEventListener('click', ()=>{
  alert('Demonstracinis atsiskaitymas. Čia parodytumėt mokėjimo langą.');
});

// prisijungimo skydelis
const loginBtn = document.getElementById('loginBtn');
const loginDrawer = document.getElementById('loginDrawer');
loginBtn?.addEventListener('click', ()=> openDrawer(loginDrawer));
document.getElementById('loginForm')?.addEventListener('submit', (e)=>{
  e.preventDefault();
  alert('Prisijungta (demo).');
  closeDrawers();
});

// maikės priekis/galas hover
document.querySelectorAll('img.prod-img[data-front][data-back]').forEach(img=>{
  const front = img.getAttribute('data-front');
  const back  = img.getAttribute('data-back');
  img.addEventListener('mouseenter', ()=> img.src = back);
  img.addEventListener('mouseleave', ()=> img.src = front);
});

// lightbox galerijai (Bootstrap karuselės img + bet kuri .car-track img)
(function(){
  const lb = document.getElementById('lightbox');
  const im = document.getElementById('lightbox-img');
  if(!lb || !im) return;

  function open(src){ im.src = src; lb.hidden=false; lb.style.display='flex'; }
  function close(){ im.src=''; lb.hidden=true; lb.style.display='none'; }

  document.querySelectorAll('#bsGallery .carousel-item img, .car-track img').forEach(el=>{
    el.style.cursor = 'zoom-in';
    el.addEventListener('click', ()=> open(el.src));
  });
  lb.addEventListener('click', close);
  document.addEventListener('keydown', e=> e.key==='Escape' && close());
})();
// --- ScrollSpy, kuris teisingai skaičiuoja fiksuotą viršutinę juostą ---
(function () {
  // bandome nuskaityti --bar-h iš CSS kintamojo; jei nepavyksta – 72px
  const barH =
    parseInt(
      getComputedStyle(document.documentElement).getPropertyValue('--bar-h')
    ) || 72;

  const nav = document.querySelector('#mainnav');
  if (!nav) return;

  const links = Array.from(nav.querySelectorAll('a[href^="#"]'));
  // susiejame nuorodas su skyriais
  const pairs = links
    .map((a) => {
      const id = a.getAttribute('href');
      const sec = document.querySelector(id);
      return sec ? { link: a, sec } : null;
    })
    .filter(Boolean);

  function setActive() {
    const pos = window.scrollY + barH + 2;
    let current = pairs[0];

    for (const p of pairs) {
      const top = p.sec.offsetTop;
      const bottom = top + p.sec.offsetHeight;
      if (pos >= top && pos < bottom) {
        current = p;
        break;
      }
    }

    pairs.forEach((p) =>
      p.link.classList.toggle('active', p === current)
    );
  }

  window.addEventListener('scroll', setActive, { passive: true });
  window.addEventListener('resize', setActive);
  window.addEventListener('load', setActive);
  setActive();
})();
// Paveikslėlių keitimas užvedus pelę / palietus
(function () {
  // Surandam visus img, turinčius abu atributus
  var swappables = document.querySelectorAll('img[data-front][data-back]');

  swappables.forEach(function (img) {
    // užtikrinam, kad pradinis src = data-front
    if (img.dataset.front && img.src.indexOf(img.dataset.front) === -1) {
      img.src = img.dataset.front;
    }

    // pelė
    img.addEventListener('mouseenter', function () {
      img.src = img.dataset.back;
    });
    img.addEventListener('mouseleave', function () {
      img.src = img.dataset.front;
    });

    // lietimas (mobilieji)
    img.addEventListener('touchstart', function () {
      img.src = img.dataset.back;
    }, { passive: true });

    img.addEventListener('touchend', function () {
      img.src = img.dataset.front;
    });
  });
})();
// temos perjungimas + išsaugojimas
(function () {
  const body = document.body;
  const btn = document.getElementById('toggleTheme');
  const saved = localStorage.getItem('theme');

  if (saved === 'dark') body.classList.add('theme-dark');

  if (btn) {
    btn.addEventListener('click', () => {
      body.classList.toggle('theme-dark');
      localStorage.setItem('theme', body.classList.contains('theme-dark') ? 'dark' : 'light');
    });
  }

  // Šrifto dydžio +/-
  const inc = document.getElementById('incFont');
  const dec = document.getElementById('decFont');
  let base = parseFloat(getComputedStyle(document.documentElement).getPropertyValue('font-size')) || 16;

  const setFS = v => { document.documentElement.style.fontSize = v + 'px'; };
  if (inc) inc.addEventListener('click', () => { base = Math.min(base + 1, 22); setFS(base); });
  if (dec) dec.addEventListener('click', () => { base = Math.max(base - 1, 12); setFS(base); });
})();
// prieinamumo meniu: atidarymas/uždarymas
(function () {
  const toggle = document.getElementById('a11yToggle');
  const menu   = document.getElementById('a11yMenu');
  if (!toggle || !menu) return;

  const openMenu  = () => { menu.hidden = false; toggle.setAttribute('aria-expanded','true'); };
  const closeMenu = () => { menu.hidden = true;  toggle.setAttribute('aria-expanded','false'); };

  toggle.addEventListener('click', (e) => {
    e.stopPropagation();
    const expanded = toggle.getAttribute('aria-expanded') === 'true';
    expanded ? closeMenu() : openMenu();
  });

  // uždaryti paspaudus šalia
  document.addEventListener('click', (e) => {
    if (!menu.hidden && !menu.contains(e.target) && e.target !== toggle) closeMenu();
  });

  // uždaryti su ESC
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeMenu();
  });

  // neleisti burbuliuoti spaudžiant pačiame meniu
  menu.addEventListener('click', (e) => e.stopPropagation());
})();
// laikmatis: rodyti datą ir laiką, su stabdymu / paleidimu
(function () {
  // apsauga: jei el. nėra, nieko nedarom
  var elText = document.getElementById('timer-text');
  var btnStop = document.getElementById('timer-stop');
  var btnStart = document.getElementById('timer-start');
  if (!elText || !btnStop || !btnStart) return;

  var tick = null;

  // atnaujina laiką ekrane
  function updateClock() {
    var now = new Date();
    // patogus vietinis formatas su data
    var date = now.toLocaleDateString('lt-LT', { year: 'numeric', month: '2-digit', day: '2-digit' });
    var time = now.toLocaleTimeString('lt-LT', { hour12: false });
    elText.textContent = date + ' ' + time;
  }

  // paleidžia intervalą
  function startClock() {
    if (tick) return;                 // jau veikia
    updateClock();                    // iškart parodyti
    tick = setInterval(updateClock, 1000);
    btnStart.disabled = true;
    btnStop.disabled  = false;
  }

  // sustabdo intervalą
  function stopClock() {
    if (!tick) return;                // jau sustabdyta
    clearInterval(tick);
    tick = null;
    btnStart.disabled = false;
    btnStop.disabled  = true;
  }

  // įvykiai mygtukams
  btnStart.addEventListener('click', startClock);
  btnStop.addEventListener('click', stopClock);

  // paleisti automatiškai įkrovus puslapį
  startClock();
})();
// laikmačio slėpimo ir rodymo valdymas
(function () {
  const toggleBtn = document.getElementById('toggleTimer');
  const timerBox  = document.getElementById('timer-widget');
  if (!toggleBtn || !timerBox) return;

  toggleBtn.addEventListener('click', () => {
    const isHidden = timerBox.classList.toggle('hidden');
    toggleBtn.setAttribute('aria-expanded', !isHidden);
  });
})();
