<script>
    // فتح مودال التعليقات وجلب التعليقات المقبولة
    function openCommentModal(itemId) {
        document.getElementById('modalItemId').value = itemId;
        const modal = document.getElementById('commentModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        const list = document.getElementById('commentsList');
        list.replaceChildren(textNode('p', 'جاري التحميل...', 'text-center text-stone-400'));

        fetch(`/comments/${itemId}`, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(comments => {
                list.replaceChildren();
                if (!comments.length) {
                    list.append(textNode('p', 'لا توجد تعليقات بعد.', 'text-center text-stone-400'));
                    return;
                }
                comments.forEach(c => {
                    const row = document.createElement('div');
                    row.className = 'border-b border-stone-100 pb-2';

                    const head = document.createElement('div');
                    head.className = 'flex justify-between items-center mb-1';
                    head.append(
                        textNode('p', c.user ? c.user.name : '', 'font-bold text-stone-800 text-sm'),
                        textNode('span', '⭐'.repeat(Number(c.rating) || 5), 'text-[10px] opacity-80')
                    );

                    row.append(head, textNode('p', c.comment, 'text-stone-600 text-xs'));
                    list.append(row);
                });
            })
            .catch(() => {
                list.replaceChildren(textNode('p', 'تعذّر تحميل التعليقات.', 'text-center text-red-500'));
            });
    }

    function closeCommentModal() {
        document.getElementById('commentModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // textContent بدل innerHTML حتى لا يُنفَّذ أي HTML داخل التعليقات أو الأسماء
    function textNode(tag, text, className) {
        const el = document.createElement(tag);
        el.textContent = text;
        el.className = className;
        return el;
    }
</script>
