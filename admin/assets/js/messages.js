    AOS.init({ once: true, duration: 420 });

    // Small search filter for the conversation list.
    document.getElementById('convSearch')?.addEventListener('input', function(e){
        const q = e.target.value.toLowerCase();
        document.querySelectorAll('.conversation-item').forEach(el=>{
            const name = el.querySelector('h6')?.innerText.toLowerCase() || '';
            el.style.display = name.includes(q) ? 'flex' : 'none';
        });
    });

    // Auto-resize composer textarea.
    document.querySelectorAll('.composer-textarea').forEach(tx=>{
        tx.addEventListener('input', ()=> {
            tx.style.height = 'auto';
            tx.style.height = (tx.scrollHeight) + 'px';
        });
    });