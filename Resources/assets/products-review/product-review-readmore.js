/*
 * Copyright 2026.  Baks.dev <admin@baks.dev>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

executeFunc(function productReviewReadmore()
{
    const textBlocks = document.querySelectorAll('.textBlock');
    const toggleBtns = document.querySelectorAll('.toggleBtn');

    textBlocks.forEach((textBlock, index) =>
    {
        const toggleBtn = toggleBtns[index];
        const btnText = toggleBtn.querySelector('span');

        let isExpanded = false;
        let animationFrame = null;


        const ROW_COUNT = 1;

        // Конфигурация анимации
        const ANIMATION_DURATION = 300; // мс
        const LINE_HEIGHT = parseFloat(getComputedStyle(textBlock).lineHeight) || 24;
        const COLLAPSED_HEIGHT = LINE_HEIGHT * ROW_COUNT; // ROW_COUNT строка

        function checkHeight()
        {
            const originalClass = textBlock.className;

            textBlock.classList.remove('truncated-text');
            textBlock.classList.add('expanded-text');
            const fullHeight = textBlock.scrollHeight;

            textBlock.classList.remove('expanded-text');
            textBlock.classList.add('truncated-text');

            const textBlockText = textBlock.innerHTML;

            if(textBlockText.length < 150)
            {
                toggleBtn.classList.add('d-none');
                textBlock.classList.add('expanded-text');
                textBlock.classList.remove('truncated-text');
            } else
            {
                toggleBtn.classList.remove('d-none');
            }
        }

        // Функция плавной анимации
        function animateHeight(startHeight, endHeight, callback)
        {
            const startTime = performance.now();

            // Отключить CSS классы на время анимации
            textBlock.classList.remove('truncated-text', 'expanded-text');
            textBlock.style.overflow = 'hidden';
            textBlock.style.display = '-webkit-box';
            textBlock.style.webkitBoxOrient = 'vertical';

            function update(currentTime)
            {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / ANIMATION_DURATION, 1);

                // Функция плавности (ease-in-out)
                const easeProgress = progress < 0.5
                    ? 2 * progress * progress
                    : 1 - Math.pow(-2 * progress + 2, 2) / 2;

                const currentHeight = startHeight + (endHeight - startHeight) * easeProgress;
                textBlock.style.height = currentHeight + 'px';

                if(progress < 1)
                {
                    animationFrame = requestAnimationFrame(update);
                } else
                {
                    // Завершение анимации
                    textBlock.style.height = '';
                    textBlock.style.overflow = '';
                    textBlock.style.display = '';
                    textBlock.style.webkitBoxOrient = '';

                    // Применяем нужный класс
                    if(endHeight > COLLAPSED_HEIGHT)
                    {
                        textBlock.classList.add('expanded-text');
                    } else
                    {
                        textBlock.classList.add('truncated-text');
                    }

                    if(callback)
                    {
                        callback();
                    }
                    animationFrame = null;
                }
            }

            animationFrame = requestAnimationFrame(update);
        }

        function toggleText()
        {
            // Отменить текущую анимацию, если она есть
            if(animationFrame)
            {
                cancelAnimationFrame(animationFrame);
                animationFrame = null;
            }

            const currentHeight = textBlock.scrollHeight;

            if(!isExpanded)
            {
                // Получить полную высоту
                textBlock.classList.remove('truncated-text');
                textBlock.classList.add('expanded-text');
                const fullHeight = textBlock.scrollHeight;

                // Возвраить обратно
                textBlock.classList.remove('expanded-text');
                textBlock.classList.add('truncated-text');

                // Анимировать от ROW_COUNT строк к полной высоте
                animateHeight(COLLAPSED_HEIGHT, fullHeight, () =>
                {
                    btnText.textContent = 'Свернуть';
                    toggleBtn.classList.add('rotated');
                });

            } else
            {
                // Анимировать от текущей высоты к ROW_COUNT строк
                animateHeight(currentHeight, COLLAPSED_HEIGHT, () =>
                {
                    btnText.textContent = 'Читать полностью';
                    toggleBtn.classList.remove('rotated');
                });
            }

            isExpanded = !isExpanded;
        }

        // Инициализация
        checkHeight();
        toggleBtn.addEventListener('click', toggleText);

    });

    return true;

});