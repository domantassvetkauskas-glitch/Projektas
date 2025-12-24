// aktyvi nuoroda pagal scroll (scrollspy)
(function(){
  const sections = document.querySelectorAll('[data-section]');
  const navLinks = document.querySelectorAll('header nav a[href^="#"]');

  if(!sections.length || !navLinks.length) return;

  function onScroll(){
    let currentId = null;
    const scrollPos = window.scrollY + 96; // header + truputį oro

    sections.forEach(sec=>{
      const rect = sec.getBoundingClientRect();
      const top = rect.top + window.scrollY;
      const bottom = top + sec.offsetHeight;

      if(scrollPos >= top && scrollPos < bottom) {
        currentId = sec.getAttribute('id');
      }
    });

    navLinks.forEach(link=>{
      const href = link.getAttribute('href') || '';
      const id = href.startsWith('#') ? href.substring(1) : null;
      if (!id) {
        link.classList.remove('active');
        return;
      }
      if (id === currentId) {
        link.classList.add('active');
      } else {
        link.classList.remove('active');
      }
    });
  }

  window.addEventListener('scroll', onScroll);
  onScroll();
})();

// sklandus scroll
document.querySelectorAll('a[href^="#"]').forEach(link=>{
  link.addEventListener('click', function(e){
    const targetId = this.getAttribute('href').substring(1);
    const target = document.getElementById(targetId);
    if(target){
      e.preventDefault();
      const y = target.getBoundingClientRect().top + window.scrollY - 80;
      window.scrollTo({ top: y, behavior: 'smooth' });
    }
  });
});

// sticky header šešėlis
(function(){
  const header = document.querySelector('header');
  if (!header) return;

  function toggleShadow() {
    if (window.scrollY > 10) {
      header.classList.add('header-scrolled');
    } else {
      header.classList.remove('header-scrolled');
    }
  }

  window.addEventListener('scroll', toggleShadow);
  toggleShadow();
})();

// mobilus meniu
(function(){
  const burger = document.getElementById('burgerBtn');
  const nav = document.querySelector('header nav');
  if(!burger || !nav) return;
  burger.addEventListener('click', ()=>{
    nav.classList.toggle('open');
  });
})();

// kalbos perjungimas (demo)
document.querySelectorAll('[data-lang]').forEach(btn=>{
  btn.addEventListener('click', ()=>{
    alert('Kalbos perjungimas – demonstracinis. Čia būtų įgyvendintas tikras vertimų mechanizmas.');
  });
});

// prieinamumo mygtukas (demo)
document.getElementById('accessibilityBtn')?.addEventListener('click', ()=>{
  alert('Prieinamumo nustatymai – demonstracinis pavyzdys.');
});

// „į krepšelį“ mygtukai (demo)
document.querySelectorAll('[data-add-to-cart]').forEach(btn=>{
  btn.addEventListener('click', ()=>{
    const name = btn.getAttribute('data-add-to-cart');
    alert('Prekė „' + name + '“ pridėta į krepšelį (demo).');
  });
});

// krepšelio mygtukas (demo)
document.getElementById('cartBtn')?.addEventListener('click', ()=>{
  alert('Krepšelio langas – demonstracinis.');
});

// drawer valdymas (login, kontaktai ir pan.)
function openDrawer(drawer) {
  if (!drawer) return;
  drawer.classList.add('open');
  document.body.classList.add('drawer-open');
}
function closeDrawers(){
  document.querySelectorAll('.drawer.open').forEach(d=> d.classList.remove('open'));
  document.body.classList.remove('drawer-open');
}
document.querySelectorAll('.drawer-close').forEach(btn=>{
  btn.addEventListener('click', closeDrawers);
});
document.getElementById('contactBtn')?.addEventListener('click', ()=>{
  const contactDrawer = document.getElementById('contactDrawer');
  openDrawer(contactDrawer);
});
document.getElementById('loginDrawerBackdrop')?.addEventListener('click', closeDrawers);

// demonstracinis „checkout“
document.getElementById('checkoutBtn')?.addEventListener('click', ()=>{
  alert('Demonstracinis atsiskaitymas. Čia parodytumėt mokėjimo langą.');
});



// maikės priekis/galas hover
document.querySelectorAll('.shirt-image[data-front][data-back]').forEach(img=>{
  const front = img.getAttribute('data-front');
  const back = img.getAttribute('data-back');
  img.addEventListener('mouseenter', ()=>{
    img.src = back;
  });
  img.addEventListener('mouseleave', ()=>{
    img.src = front;
  });
});

// kontaktų forma (demo)
document.getElementById('contactForm')?.addEventListener('submit', (e)=>{
  // jei php apdorojama – galima išjungti preventDefault
  // dabar paliekam demui
  e.preventDefault();
  alert('Ačiū! Jūsų žinutė išsiųsta (demo).');
  closeDrawers();
});

// iškeliam „į viršų“ mygtuką
(function(){
  const btn = document.getElementById('scrollTopBtn');
  if(!btn) return;
  window.addEventListener('scroll', ()=>{
    if(window.scrollY > 300){
      btn.classList.add('visible');
    } else {
      btn.classList.remove('visible');
    }
  });
  btn.addEventListener('click', ()=>{
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
})();

// filtravimo ir paieškos (demo)
(function(){
  const searchInput = document.getElementById('productSearch');
  const cards = document.querySelectorAll('[data-product-card]');
  if(!searchInput || !cards.length) return;
  searchInput.addEventListener('input', ()=>{
    const q = searchInput.value.toLowerCase();
    cards.forEach(card=>{
      const text = card.innerText.toLowerCase();
      card.style.display = text.includes(q) ? '' : 'none';
    });
  });
})();

// prieinamumo „font-size“ (demo)
(function(){
  const incBtn = document.getElementById('fontIncBtn');
  const decBtn = document.getElementById('fontDecBtn');
  let size = 100;
  function setSize(val){
    size = Math.min(130, Math.max(80, val));
    document.documentElement.style.fontSize = size + '%';
  }
  incBtn?.addEventListener('click', ()=> setSize(size + 10));
  decBtn?.addEventListener('click', ()=> setSize(size - 10));
})();

// lietuvių/vėliavos mygtukas (demo)
document.getElementById('flagLt')?.addEventListener('click', ()=>{
  alert('Kalba jau LT (demo).');
});
document.getElementById('flagEn')?.addEventListener('click', ()=>{
  alert('EN kalba (demo). Čia būtų perjungtos etiketės.');
});

// „rodyti daugiau“ apie mus (demo)
(function(){
  const moreBtn = document.getElementById('aboutMoreBtn');
  const moreText = document.getElementById('aboutMoreText');
  if(!moreBtn || !moreText) return;
  moreBtn.addEventListener('click', ()=>{
    const open = moreText.classList.toggle('open');
    moreBtn.textContent = open ? 'Rodyti mažiau' : 'Skaityti daugiau';
  });
})();

// kontaktų kartė (demo)
document.getElementById('mapBtn')?.addEventListener('click', ()=>{
  alert('Čia galėtų atsidaryti Google Maps (demo).');
});

// SEO / accessibility: skip to content
(function(){
  const skipLink = document.getElementById('skipToContent');
  if (!skipLink) return;
  skipLink.addEventListener('click', (e)=>{
    const main = document.querySelector('main');
    if(main){
      e.preventDefault();
      main.setAttribute('tabindex','-1');
      main.focus();
      window.scrollTo({ top: main.offsetTop, behavior: 'smooth' });
    }
  });
})();
