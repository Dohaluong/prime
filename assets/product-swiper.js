document.querySelectorAll('.gallery').forEach((gallery, number) => {
  if (typeof Swiper === 'undefined') return;
  const main = gallery.querySelector('.main-photo'), thumbs = gallery.querySelector('.thumbs'), demo = gallery.querySelector('.demo'), video = demo?.querySelector('iframe');
  const images = [...(thumbs?.querySelectorAll('img') || [])].map(img => img.src);
  if (!main || !thumbs || !images.length) return;
  const lightbox = typeof FsLightbox === 'function' ? new FsLightbox() : null;
  if (lightbox) lightbox.props.sources = images;
  // Put the clip immediately after the primary and secondary photos. Any extra
  // product media follows it, while a selected material image is always appended.
  const videoIndex = video ? Math.min(2, images.length) : -1;
  const imageSlide = (src, i) => `<div class="swiper-slide"><button class="gallery-main-lightbox" data-lightbox-index="${i}" type="button"><img src="${src}" alt="Ảnh sản phẩm"></button></div>`;
  const slides = images.slice(0, videoIndex < 0 ? images.length : videoIndex).map(imageSlide);
  if (video) slides.push(`<div class="swiper-slide product-video-slide"><iframe src="${video.src}" title="Video sản phẩm" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe></div>`);
  if (video) slides.push(...images.slice(videoIndex).map((src, i) => imageSlide(src, i + videoIndex)));
  main.innerHTML = `<div class="swiper product-main-swiper"><div class="swiper-wrapper">${slides.join('')}</div><button class="swiper-button-prev" aria-label="Ảnh trước"><span aria-hidden="true">‹</span></button><button class="swiper-button-next" aria-label="Ảnh tiếp theo"><span aria-hidden="true">›</span></button></div>`;
  const youtubeId = video?.src.match(/(?:embed\/|youtu\.be\/|v=)([\w-]{6,})/)?.[1];
  const videoThumbnail = youtubeId ? `https://i.ytimg.com/vi/${youtubeId}/hqdefault.jpg` : images[0];
  const thumbSlide = (src, sourceIndex, slideIndex) => `<div class="swiper-slide"><button class="gallery-thumb ${slideIndex === 0 ? 'active' : ''}" data-slide-index="${slideIndex}" type="button"><img src="${src}" alt="Ảnh thu nhỏ"></button></div>`;
  const thumbSlides = images.slice(0, videoIndex < 0 ? images.length : videoIndex).map((src, i) => thumbSlide(src, i, i));
  if (video) thumbSlides.push(`<div class="swiper-slide"><button class="gallery-thumb gallery-video-thumb" data-slide-index="${videoIndex}" type="button"><img src="${videoThumbnail}" alt="Xem clip"><i class="bi bi-play-fill"></i></button></div>`);
  if (video) thumbSlides.push(...images.slice(videoIndex).map((src, i) => thumbSlide(src, i + videoIndex, i + videoIndex + 1)));
  thumbs.innerHTML = `<div class="swiper product-thumb-swiper"><div class="swiper-wrapper">${thumbSlides.join('')}</div></div>`; demo.hidden = true;
  const thumbSwiper = new Swiper(thumbs.querySelector('.product-thumb-swiper'), {slidesPerView:4, spaceBetween:12, watchOverflow:true, breakpoints:{0:{slidesPerView:3},640:{slidesPerView:4}}});
  const mainSwiper = new Swiper(main.querySelector('.product-main-swiper'), {slidesPerView:1, spaceBetween:0, loop:false, navigation:{nextEl:main.querySelector('.swiper-button-next'),prevEl:main.querySelector('.swiper-button-prev')}});
  const active = index => {thumbs.querySelectorAll('.gallery-thumb').forEach((button,i)=>button.classList.toggle('active',i===index)); thumbSwiper.slideTo(Math.max(0,index-1));};
  mainSwiper.on('slideChange', () => { const i=mainSwiper.activeIndex; active(i); if(i===videoIndex && video){const frame=mainSwiper.slides[i].querySelector('iframe'); const url=new URL(frame.src,location.href);url.searchParams.set('autoplay','1');frame.src=url;}});
  thumbs.addEventListener('click', event => {const button=event.target.closest('.gallery-thumb');if(!button)return;mainSwiper.slideTo(Number(button.dataset.slideIndex));});
  main.addEventListener('click', event => {const button=event.target.closest('.gallery-main-lightbox');if(button)lightbox?.open(Number(button.dataset.lightboxIndex));});
  let materialSlideIndex = null;
  window.primeGalleryShowImage = src => {
    if (materialSlideIndex !== null) {
      images[materialSlideIndex] = src;
      mainSwiper.slides[materialSlideIndex]?.querySelector('img')?.setAttribute('src', src);
      thumbSwiper.slides[materialSlideIndex]?.querySelector('img')?.setAttribute('src', src);
      if (lightbox) lightbox.props.sources = images;
      mainSwiper.slideTo(materialSlideIndex);
      return;
    }
    let i = images.indexOf(src);
    if (i >= 0) { mainSwiper.slideTo(video && i >= videoIndex ? i + 1 : i); return; }
    i = images.length; images.push(src); materialSlideIndex = mainSwiper.slides.length;
    const createSlide = (thumbnail=false) => { const slide=document.createElement('div');slide.className='swiper-slide';const button=document.createElement('button');button.type='button';button.className=thumbnail?'gallery-thumb':'gallery-main-lightbox';button.dataset[thumbnail?'slideIndex':'lightboxIndex']=String(thumbnail ? materialSlideIndex : i);const image=document.createElement('img');image.src=src;image.alt='Ảnh vật liệu';image.loading='eager';button.append(image);slide.append(button);return slide; };
    mainSwiper.wrapperEl.append(createSlide()); thumbSwiper.wrapperEl.append(createSlide(true));
    mainSwiper.update(); thumbSwiper.update(); if(lightbox)lightbox.props.sources=images;
    requestAnimationFrame(()=>mainSwiper.slideTo(i,0));
  };
});
