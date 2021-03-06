<?php
return [
    // model
    'post.attr.url'             =>  'Url',
    'post.attr.title'           =>  'عنوان',
    'post.attr.content'         =>  'محتوی',
    'post.attr.pin'             =>  'پین',
    'post.attr.comments_count'  =>  'تعداد کامنت ها',
    'post.attr.status'          =>  'وضعیت',
    'post.attr.created_at'      =>  'نوشته شده در',
    'post.attr.updated_at'      =>  'بروزرسانی',
    
    'user._user.othermedia'     =>  'دیگر رسانه ها',
    'user._user.posts'          =>  'نوشته ها',
    'user._user.recommended'    =>  'توصیه ها',
    'user._user.followers'      =>  'دنبال کنندگان',
    'user._user.following'      =>  'دنبال شوندگان',
    '_follow.unfollow'          =>  'دنبال شده',
    '_follow.follow'            =>  'دنبال کن',
    '_follow.editMyProfile'     =>  'بروزرسانی پروفایل',
    
    'header.save'               =>  'ذخیره',
    'header.preview'            =>  'پیش نمایش',
    'header.publish'            =>  'انتشار',
    'header.view.edit'          =>  'ویرایش',
    'header.view.recommend'     =>  'توصیه',
    'header.view.recommended'   =>  'توصیه شده',
    'header.load.autosave'      =>  'بازیابی از ذخیره خودکار',
    'header.load.saved'         =>  'بازیابی از ذخیره',
    
    'write.title.placeholder'   =>  'عنوان',
    'write.body.placeholder'    =>  'متن',
    'write.embed.placeholder'   =>  'لینک ویدئو را از آپارت،Youtube یا Vimeo کپی کنید.',
    'write.extract.placeholder' =>  'لینک صفحه ای از یک سایت دیگر را کپی کنید تا استخراج شود.',
    'write.save.error.title'    =>  'در ذخیره نوشته شما مشکلی به وجود آمده است،اطمینان حاصل نمایید که عنوان و متن نوشته خالی نمی باشد.',
    'post.written_by'           =>  'نوشته شده در {time} توسط {author}',
    
    'comment.submitBtn'         =>  'ارسال نظر',
    'comment.delete'            =>  'حذف نظر',
    
    'admin.status.draft'        =>  'پیش نویس ها',
    'admin.status.publish'      =>  'منتشر شده ها',
    'admin.status.trash'        =>  'زباله دان',
    'admin.new.post'            =>  'یک نوشته جدید',
    
    
    '_admin.btn.comment.title'  =>  'مشاهده {count} نظر ارسال شده برای این نوشته',
    '_admin.btn.stat.title'     =>  'آمار',
    'ـadmin.btn.view.title'      =>  'مشاهده',
    'ـadmin.btn.preview.title'   =>  'پیش نمایش',
    'ـadmin.btn.edit.title'      =>  'ویرایش',
    'ـadmin.btn.pin.title'      =>  'سنجاق زدن',
    'ـadmin.btn.unpin.title'    =>  'کندن سنجاق',
    '_admin.btn.trash.title'    =>  'انتقال به زباله دان',
    '_admin.btn.untrash.title'  =>  'بازگرداندن به پیش نویس ها',
    '_admin.btn.delete.title'   =>  'حذف دائمی',
    '_admin.btn.delete.dataconfirm' =>  'آیا از حذف کامل این نوشته اطمینان دارید؟بعد از تایید پست شما بصورت کامل حذف می گردد و نمی توانید آن را بازگردانید.',
    '_admin.title.null'         =>  'بدون عنوان',
    '_admin.content.null'       =>  'بدون متن',
    
    '_show_post_abstract.written_by'                =>  'نوشته شده در {time} توسط ',
    '_show_post_abstract.recommended_by'            =>  '،توصیه شده توسط ',
    '_show_post_abstract.comma'                     =>  '، ',
    '_show_post_abstract.recommended_by_some_more'  =>  ' و {counter} نفر دیگر',
    'comment.loginToComment'    =>  'وارد شوید و نظر دهید',
    
    //
    'coverPhotoForm.attr.coverImage'        =>  'کاور',
    'coverPhotoForm.err.try_later'          =>  'در حال حاضر امکان این تغییر وجود ندارد،لطفا دقایقی دیگر دوباره امتحان نمایید.',
    'write.cover.title'                     =>  'تنظیمات کاور',
    'write.cover.image.new'                 =>  'انتخاب',
    'write.cover.image.change'              =>  'تغییر',
    'write.cover.image.remove'              =>  'پاک کردن',
    'post.cover.add'                        =>  'افزودن/جایگزینی کاور',
    'post.cover.remove'                     =>  'حذف کاور',
    'cover.remove.successful'               =>  'کاور پست با موفقیت حذف گردید،این پست بدون کاور نمایش داده می شود.',
    'cover.add_or_change.successful'        =>  'کاور با موفقیت تغییر یافت،نمایش این تغییر ممکن است تا چند دقیقه زمان ببرد.',

    'write.tags.title'                      =>  'برچسب‌ها',

    
    'home._signup_suggestion.title'         =>  'خوش آمدید',
    'home._signup_suggestion.text'          =>  'برای انتشار نوشته های خود و استفاده بهتر از امکانات، عضو و یا وارد شوید.',
    'home._signup_suggestion.btn.register'  =>  'عضویت',
    'home._signup_suggestion.btn.login'     =>  'ورود',
    
    'home.index.head.title'                 =>  Yii::t('app','title').' - لذت خواندن و خوانده شدن',
    'user.user.head.title'                  =>  '{name} - '.Yii::t('app','title'),    
    'write.head.title.new'                  =>  'یک نوشته جدید - '.Yii::t('app','title'),
    'write.head.title.edit'                 =>  'ویرایش {title} - '.Yii::t('app','title'),
    'view.head.title'                       =>  '{title} - '.Yii::t('app','title'),
    'preview.head.title.set'                =>  '{title} - '.Yii::t('app','title'),
    'preview.head.title.unknown'            =>  'بدون عنوان - '.Yii::t('app','title'),
    'admin.head.title'                      =>  'مدیریت نوشته ها',
    
    'meta._home.site_name'                  =>  'نودز',
    'meta._home.title'                      =>  'نودز، لذت بی نهایت خواندن و خوانده شدن',
    'meta._home.description'                =>  'نودز یک بستر ساده و کاربر پسند برای خواندن و نوشتن می باشد.شما می توانید به راحتی بنویسید،به دیگران توصیه کنید و به راحتی نوشته های دیگران را بخوانید.',
    'meta._user.description'                =>  'نوشته های {title} در نودز - {description}',
    
    'home._posts_list.no_post'              =>  'متاسفیم، ولی نوشته ای برای نمایش وجود ندارد!',
    
    'home._alert.activation.header'         =>  'فعال سازی حساب کاربری',
    'home._alert.activation.text'           =>  'حساب کاربری شما با موفقیت فعال شد.منتظر انتشار نوشته های شما هستیم.',
    'home._alert.reset.header'              =>  'نوسازی کلمه عبور',
    'home._alert.reset.text'                =>  'کلمه عبور جدید با موفقیت جایگزین شد.',
];  