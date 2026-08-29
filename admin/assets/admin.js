document.querySelector('[data-product-search]')?.addEventListener('input',e=>{const q=e.target.value.toLowerCase();document.querySelectorAll('[data-product-row]').forEach(row=>row.hidden=!row.dataset.name.includes(q));});
document.querySelectorAll('[data-filter]').forEach(button=>button.addEventListener('click',()=>{document.querySelectorAll('[data-filter]').forEach(x=>x.classList.remove('active'));button.classList.add('active');document.querySelectorAll('[data-product-row]').forEach(row=>{const f=button.dataset.filter;row.hidden=f==='fast'?row.dataset.fast!=='1':f==='hidden'?row.dataset.active!=='0':false;});}));
document.querySelector('[data-taxonomy-search]')?.addEventListener('input',e=>{const q=e.target.value.toLowerCase();document.querySelectorAll('[data-taxonomy-row]').forEach(row=>row.hidden=!row.dataset.search.includes(q));});
document.querySelectorAll('.media-thumb input').forEach(input=>input.addEventListener('change',()=>{document.querySelectorAll('.media-thumb').forEach(item=>item.classList.remove('selected'));input.closest('.media-thumb').classList.add('selected');}));
document.querySelectorAll('.media-grid').forEach(grid=>{
  const productId=new URLSearchParams(location.search).get('id');
  if(!productId)return;
  let dragged=null;
  const thumbs=()=>[...grid.querySelectorAll('.media-thumb')];
  grid.querySelectorAll('.media-thumb').forEach(thumb=>{
    thumb.draggable=true;
    thumb.addEventListener('dragstart',event=>{dragged=thumb;thumb.classList.add('dragging');event.dataTransfer.effectAllowed='move';});
    thumb.addEventListener('dragend',()=>{thumb.classList.remove('dragging');grid.querySelectorAll('.media-thumb').forEach(item=>item.classList.remove('drag-over'));dragged=null;});
    thumb.addEventListener('dragover',event=>{event.preventDefault();if(thumb!==dragged)thumb.classList.add('drag-over');});
    thumb.addEventListener('dragleave',()=>thumb.classList.remove('drag-over'));
    thumb.addEventListener('drop',async event=>{event.preventDefault();thumb.classList.remove('drag-over');if(!dragged||dragged===thumb)return;const list=thumbs(),from=list.indexOf(dragged),to=list.indexOf(thumb);if(from<to)thumb.after(dragged);else thumb.before(dragged);const order=thumbs().map(item=>item.querySelector('input[name="featured_image"]')?.value).filter(Boolean);await fetch('product-images-reorder.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:new URLSearchParams({product_id:productId,order:JSON.stringify(order)})});});
  });
});
document.querySelector('[data-material-search]')?.addEventListener('input',e=>{const q=e.target.value.toLowerCase();document.querySelectorAll('[data-material-row]').forEach(row=>row.hidden=!row.dataset.search.includes(q));});
const materialForm=document.querySelector('form[action="material-save.php"]');if(materialForm){const name=materialForm.querySelector('input[name="name"]'),code=materialForm.querySelector('input[name="code"]'),slugify=v=>v.normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().replace(/đ/g,'d').replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'');name?.addEventListener('input',()=>code.value=slugify(name.value));}

const productForm=document.querySelector('form.product-form');
if(productForm){
  const category=productForm.querySelector('select[name="type"]'),name=productForm.querySelector('input[name="name"]'),slug=productForm.querySelector('input[name="slug"]'),productId=new URLSearchParams(location.search).get('id')||'';
  if(category&&name&&slug)fetch('taxonomy-options.php?product_id='+encodeURIComponent(productId)).then(response=>response.json()).then(data=>{
    const slugify=value=>value.normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().replace(/đ/g,'d').replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'');
    const previous=category.value;
    category.innerHTML=data.categories.length?data.categories.map(item=>'<option value="'+item.id+'" data-name="'+String(item.name).replace(/"/g,'&quot;')+'" data-code="'+String(item.code).replace(/"/g,'&quot;')+'">'+item.name+'</option>').join(''):'<option value="">Chưa có Category</option>';
    category.name='category_id';
    const selected=data.selected?.category_id||data.categories.find(item=>item.name===previous)?.id;
    if(selected)category.value=selected;
    const field=document.createElement('div');field.className='field';field.innerHTML='<label>Collection *</label><select required name="collection_id"></select>';category.closest('.field').after(field);
    const collection=field.querySelector('select');
    collection.innerHTML=data.collections.length?'<option value="">Chọn Collection</option>'+data.collections.map(item=>'<option value="'+item.id+'" data-name="'+String(item.name).replace(/"/g,'&quot;')+'" data-code="'+String(item.code).replace(/"/g,'&quot;')+'">'+item.name+'</option>').join(''):'<option value="">Chưa có Collection</option>';
    if(data.selected?.collection_id)collection.value=data.selected.collection_id;
    const update=()=>{const cat=category.selectedOptions[0],col=collection.selectedOptions[0];if(!cat?.value||!col?.value)return;name.value=cat.dataset.name+' '+col.dataset.name.replace(/^Bộ sưu tập\s*/i,'');slug.value=slugify(cat.dataset.code+'-'+col.dataset.code);};
    category.addEventListener('change',update);collection.addEventListener('change',update);
  });
}

document.querySelectorAll('[data-spec-rows]').forEach(tbody=>{
  const card=tbody.closest('.content-card')||document;
  const addBtn=card.querySelector('[data-spec-add]'),sampleBtn=card.querySelector('[data-spec-sample]');
  const addRow=(label='',value='')=>{
    const tr=document.createElement('tr');
    tr.innerHTML='<td><input type="text" name="spec_label[]" placeholder="Ví dụ: Kích thước tổng thể"></td><td><input type="text" name="spec_value[]" placeholder="Ví dụ: 220 × 95 × 85 cm"></td><td><button type="button" class="spec-remove" aria-label="Xoá dòng"><i class="bi bi-trash"></i></button></td>';
    tr.querySelector('[name="spec_label[]"]').value=label;
    tr.querySelector('[name="spec_value[]"]').value=value;
    tr.querySelector('.spec-remove').addEventListener('click',()=>tr.remove());
    tbody.appendChild(tr);
  };
  tbody.querySelectorAll('.spec-remove').forEach(btn=>btn.addEventListener('click',()=>btn.closest('tr').remove()));
  tbody._addSpecRow=addRow;
  addBtn?.addEventListener('click',()=>addRow());
  sampleBtn?.addEventListener('click',()=>{
    const sample=[
      ['Kích thước tổng thể','220 × 95 × 85 cm (dài × sâu × cao)'],
      ['Kích thước khi mở rộng','Rộng 200 – 360 cm'],
      ['Chiều cao tay vịn','53 cm'],
      ['Chiều cao mặt ngồi','43 cm'],
      ['Độ sâu mặt ngồi','67 cm (tối đa 100 cm khi ngả)'],
      ['Chất liệu khung & đệm','Khung gỗ thông nhập khẩu, đệm mút HD45, lò xo túi 4cm, đai dệt 10cm, lớp vải lót bảo vệ'],
      ['Motor điện','Đạt chuẩn an toàn SGS, có pin dự phòng khi mất điện'],
      ['Trọng lượng','~50 kg / chỗ ngồi'],
      ['Số chỗ ngồi','3 – 6 người'],
      ['Phụ kiện đi kèm','Điều khiển từ xa, 2 gối tựa'],
      ['Lắp đặt','Miễn phí lắp đặt tại nhà nội thành Hà Nội'],
      ['Bảo hành','Khung 10 năm · motor 2 năm · vải 1 năm']
    ];
    sample.forEach(([label,value])=>addRow(label,value));
  });
});
if(window.jQuery){
  jQuery('.rich-editor').each(function(){
    jQuery(this).summernote({
      height:260,
      placeholder:'Nhập nội dung...',
      toolbar:[['style',['bold','italic','underline']],['para',['ul','ol','paragraph']],['insert',['link','picture']],['view',['codeview']]]
    });
  });
}
document.querySelectorAll('[data-stat-rows]').forEach(tbody=>{
  const card=tbody.closest('.form-card')||document;
  const addBtn=card.querySelector('[data-stat-add]');
  const addRow=(number='',label='')=>{
    const tr=document.createElement('tr');
    tr.innerHTML='<td><input type="text" name="stat_number[]" placeholder="Ví dụ: 10 năm"></td><td><input type="text" name="stat_label[]" placeholder="Ví dụ: bảo hành khung"></td><td><button type="button" class="spec-remove" aria-label="Xoá dòng"><i class="bi bi-trash"></i></button></td>';
    tr.querySelector('[name="stat_number[]"]').value=number;
    tr.querySelector('[name="stat_label[]"]').value=label;
    tr.querySelector('.spec-remove').addEventListener('click',()=>tr.remove());
    tbody.appendChild(tr);
  };
  tbody.querySelectorAll('.spec-remove').forEach(btn=>btn.addEventListener('click',()=>btn.closest('tr').remove()));
  addBtn?.addEventListener('click',()=>addRow());
});
document.querySelectorAll('.about-image-field input[type="file"]').forEach(input=>input.addEventListener('change',()=>{
  const file=input.files[0]; if(!file)return;
  const preview=input.closest('.about-image-field')?.querySelector('.about-image-preview');
  if(!preview)return;
  const reader=new FileReader();
  reader.onload=()=>{preview.innerHTML='<img src="'+reader.result+'" alt="">';};
  reader.readAsDataURL(file);
}));
document.querySelectorAll('[data-ai-generate]').forEach(button=>button.addEventListener('click',async()=>{
  const form=button.closest('form');
  const name=form?.querySelector('input[name="name"]')?.value.trim()||'';
  if(!name){alert('Hãy nhập tên sản phẩm trước khi tạo nội dung bằng AI.');return;}
  const tbody=document.querySelector('[data-spec-rows]');
  const hasContent=(window.jQuery&&jQuery('#detailed-description-editor').summernote('code').replace(/<[^>]*>/g,'').trim())||(tbody&&tbody.children.length);
  if(hasContent&&!confirm('Nội dung hiện có sẽ bị thay thế bằng nội dung do AI tạo. Tiếp tục?'))return;
  const original=button.innerHTML;
  button.disabled=true;button.innerHTML='<i class="bi bi-hourglass-split"></i> Đang tạo...';
  try{
    const body=new URLSearchParams({
      name,
      type:form.querySelector('select[name="type"],select[name="category_id"]')?.selectedOptions?.[0]?.textContent?.trim()||'',
      description:form.querySelector('textarea[name="description"]')?.value||'',
      price:form.querySelector('input[name="price"]')?.value||''
    });
    const res=await fetch('product-ai-generate.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body});
    const data=await res.json();
    if(!data.ok)throw new Error(data.error||'Không tạo được nội dung.');
    if(window.jQuery&&document.getElementById('detailed-description-editor')){
      jQuery('#detailed-description-editor').summernote('code',data.detailed_description);
    }
    if(tbody&&tbody._addSpecRow){
      tbody.innerHTML='';
      (data.specifications||[]).forEach(spec=>tbody._addSpecRow(spec.label,spec.value));
    }
  }catch(err){
    alert(err.message||'Có lỗi khi gọi AI, hãy thử lại.');
  }finally{
    button.disabled=false;button.innerHTML=original;
  }
}));

const variantsForm=document.querySelector('form.product-form');
if(variantsForm){
  const productId=new URLSearchParams(location.search).get('id'),section=document.createElement('section');
  section.className='variants-section';variantsForm.after(section);
  const post=(url,data)=>fetch(url,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:new URLSearchParams(data)});
  const esc=value=>String(value||'').replace(/[&<>"']/g,char=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char]));
  const modal=(title,body)=>{const el=document.createElement('div');el.className='variant-modal';el.innerHTML='<div class="variant-modal-backdrop"></div><section class="variant-modal-card"><button type="button" class="variant-modal-close" aria-label="Đóng">×</button><h3>'+esc(title)+'</h3>'+body+'</section>';document.body.append(el);el.querySelectorAll('.variant-modal-close,.variant-modal-backdrop').forEach(x=>x.addEventListener('click',()=>el.remove()));return el;};
  const dimensions=(data={})=>'<div class="variant-dimensions"><label>Dài (mm)<input name="length" type="number" min="0" value="'+esc(data.length||'')+'"></label><label>Rộng (mm)<input name="width" type="number" min="0" value="'+esc(data.width||'')+'"></label><label>Cao (mm)<input name="height" type="number" min="0" value="'+esc(data.height||'')+'"></label></div>';
  const sizeEditor=(size,data)=>{const d=size?.details||{},el=modal(size?'Chi tiết kích thước':'Thêm cột kích thước','<form class="variant-detail-form"><label>Tên option *<input required name="name" value="'+esc(size?.name||'')+'" placeholder="Ví dụ: 2.1m"></label>'+dimensions(d)+'<label>Ghi chú<textarea name="note" rows="3" placeholder="Ví dụ: kích thước phủ bì khi đóng">'+esc(d.note||'')+'</textarea></label><div class="variant-modal-actions">'+(size?'<button class="variant-delete" type="button">Xoá cột</button>':'')+'<button class="admin-button" type="submit">Lưu thông tin</button></div></form>');
    const form=el.querySelector('form');form.addEventListener('submit',async e=>{e.preventDefault();const fd=new FormData(form),details={length:fd.get('length'),width:fd.get('width'),height:fd.get('height'),note:fd.get('note')};await post(size?'variants-save-size.php':'variants-add-size.php',{product_id:productId,id:size?.id||'',name:fd.get('name'),details:JSON.stringify(details)});el.remove();load();});el.querySelector('.variant-delete')?.addEventListener('click',async()=>{if(confirm('Xoá cột kích thước này và toàn bộ giá liên quan?')){await post('variants-delete-size.php',{product_id:productId,id:size.id});el.remove();load();}});};
  const materialEditor=(row,data)=>{const colors=data.colors.filter(color=>String(color.material_id)===String(row.id)),picked=row.color_ids?.map(String)||[];const choices=colors.length?colors.map(color=>'<label class="variant-color-choice"><input type="checkbox" value="'+color.id+'" '+(!picked.length||picked.includes(String(color.id))?'checked':'')+'><i style="background:'+esc(color.hex_code||'#ddd')+'"></i><span>'+esc(color.code)+'</span></label>').join(''):'<p class="variant-no-colors">Chất liệu này chưa có màu.</p>';const el=modal('Chi tiết chất liệu: '+row.name,'<form class="variant-detail-form"><p class="variant-modal-note">Chọn các màu được phép hiển thị cho sản phẩm này.</p><div class="variant-color-choices">'+choices+'</div><div class="variant-modal-actions"><button class="variant-delete" type="button">Xoá option vật liệu</button><button class="admin-button" type="submit">Lưu lựa chọn màu</button></div></form>');const form=el.querySelector('form');form.addEventListener('submit',async e=>{e.preventDefault();const ids=[...form.querySelectorAll('input:checked')].map(x=>x.value);await post('variants-save-material.php',{product_id:productId,material_id:row.id,color_ids:JSON.stringify(ids)});el.remove();load();});el.querySelector('.variant-delete').addEventListener('click',async()=>{if(confirm('Xoá chất liệu này khỏi ma trận biến thể?')){await post('variants-delete-material.php',{product_id:productId,material_id:row.id});el.remove();load();}});};
  const autoPrice=async data=>{const base=Number(data.formula?.price||0),category=Number(data.formula?.category_coefficient||1),collection=Number(data.formula?.collection_coefficient||1),missing=data.sizes.filter(size=>!Number(size.details?.length)||!Number(size.details?.width));if(!base){alert('Hãy lưu giá niêm yết sản phẩm trước khi tính tự động.');return;}if(missing.length){alert('Cần nhập Dài và Rộng (mm) trong chi tiết của: '+missing.map(size=>size.name).join(', '));return;}if(!confirm('Tự động điền lại toàn bộ giá biến thể? Bạn vẫn có thể sửa từng ô sau đó.'))return;const requests=[];data.rows.forEach(row=>data.sizes.forEach(size=>{const length=Number(size.details.length),width=Number(size.details.width),meters=(length+(width>1000?width-1000:0))/1000,raw=base*meters*category*collection*Number(row.coefficient||1),price=Math.round(raw/100000)*100000;requests.push(post('variants-save-price.php',{product_id:productId,material_id:row.id,size_id:size.id,price:price}));}));await Promise.all(requests);load();};
  const render=data=>{if(!productId){section.innerHTML='<div class="variants-head"><div><div class="kicker">Biến thể</div><h3>Giá theo kích thước và chất liệu</h3><p>Hãy lưu sản phẩm trước, sau đó thêm biến thể.</p></div></div>';return;}const available=data.materials.filter(m=>!data.rows.some(r=>String(r.id)===String(m.id))),headers=data.sizes.map(s=>'<th><button type="button" data-size="'+s.id+'">'+esc(s.name)+'</button></th>').join(''),rows=data.rows.map(row=>'<tr><th><button type="button" data-material="'+row.id+'"><b>'+esc(row.name)+'</b><small>'+esc(row.code)+'</small></button></th>'+data.sizes.map(size=>{const price=data.prices[row.id+'_'+size.id]??'';return '<td><input type="number" min="0" data-variant-price data-material="'+row.id+'" data-size="'+size.id+'" value="'+esc(price)+'" placeholder="—"><span>đ</span></td>';}).join('')+'</tr>').join('');
    section.innerHTML='<div class="variants-head"><div><div class="kicker">Biến thể</div><h3>Giá theo kích thước và chất liệu</h3><p>Nhấp tiêu đề cột hoặc dòng để chỉnh thông tin chi tiết, màu hiển thị và xoá option.</p></div><div class="variant-actions"><button type="button" class="admin-button auto-variant" data-auto-price><i class="bi bi-calculator"></i> Tính giá tự động</button><button type="button" class="admin-button outline-variant" data-add-size><i class="bi bi-columns-gap"></i> Thêm cột</button><select data-material-choice><option value="">Chọn chất liệu</option>'+available.map(m=>'<option value="'+m.id+'">'+esc(m.name)+'</option>').join('')+'</select><button type="button" class="admin-button" data-add-material><i class="bi bi-plus-lg"></i> Thêm dòng</button></div></div><div class="variant-table-wrap">'+(data.sizes.length&&data.rows.length?'<table class="variant-table"><thead><tr><th>Chất liệu / Kích thước</th>'+headers+'</tr></thead><tbody>'+rows+'</tbody></table>':'<div class="variant-empty">Thêm ít nhất một cột kích thước và một dòng chất liệu để tạo bảng giá.</div>')+'</div>';
    section.querySelector('[data-auto-price]')?.addEventListener('click',()=>autoPrice(data));section.querySelector('[data-add-size]')?.addEventListener('click',()=>sizeEditor(null,data));section.querySelectorAll('button[data-size]').forEach(button=>button.addEventListener('click',()=>sizeEditor(data.sizes.find(s=>String(s.id)===button.dataset.size),data)));section.querySelectorAll('button[data-material]').forEach(button=>button.addEventListener('click',()=>materialEditor(data.rows.find(row=>String(row.id)===button.dataset.material),data)));section.querySelector('[data-add-material]')?.addEventListener('click',async()=>{const id=section.querySelector('[data-material-choice]').value;if(id){await post('variants-add-material.php',{product_id:productId,material_id:id});load();}});section.querySelectorAll('[data-variant-price]').forEach(input=>input.addEventListener('change',()=>post('variants-save-price.php',{product_id:productId,material_id:input.dataset.material,size_id:input.dataset.size,price:input.value})));};
  const load=()=>fetch('variants-data.php?product_id='+encodeURIComponent(productId||'')).then(r=>r.json()).then(render);load();
}
