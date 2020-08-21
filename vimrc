scriptencoding utf-8
set encoding=utf-8
set tabstop=4
set shiftwidth=4
set nu
set mouse=a
syntax on
set list lcs=trail:·,tab:>\ ,eol:⏎

colorscheme desert
hi SpecialKey ctermfg=8
hi LineNr ctermfg=8
hi NonText ctermfg=8

autocmd VimEnter * NERDTree
autocmd VimEnter * wincmd p

if has("clipboard")
  set clipboard=unnamed
  if has("unnamedplus")
    set clipboard+=unnamedplus
  endif
endif  
