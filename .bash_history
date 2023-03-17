#1663032788
ll
#1663032789
cd public_html/
#1663032790
ll
#1663035317
rm index.html 
#1663035318
ll
#1663035324
tar -xfz ./gnuboard-gnuboard5-v5.5.8.2.1-0-gf1a303e.tar.gz 
#1663035333
tar -xf ./gnuboard-gnuboard5-v5.5.8.2.1-0-gf1a303e.tar.gz 
#1663035335
ll
#1663035340
cd gnuboard-gnuboard5-f1a303e/
#1663035341
ll
#1663035343
cd ..
#1663035343
ll
#1663035360
rm gnuboard-gnuboard5-v5.5.8.2.1-0-gf1a303e.tar.gz 
#1663035361
ll
#1663035363
cd gnuboard-gnuboard5-f1a303e/
#1663035364
ll
#1663035368
mv ./* ../
#1663035368
ll
#1663035370
cd ..
#1663035371
ll
#1663035376
rm -rf ./gnuboard-gnuboard5-f1a303e/
#1663035376
ll
#1663035383
mkdir data
#1663035388
chmod 755 data
#1663035389
ll
#1663035401
chmod 707 data
#1663042709
ll
#1663042710
exit
#1663122837
su -
#1663144205
exit
#1666329257
ll
#1666329258
cd public_html/
#1666329259
ll
#1666329264
find . -name "*.*" | xargs grep "G5_IS_MOBILE"
#1666329297
su -
#1666329388
exit
#1667871104
su -
#1667871125
exit
#1669362735
su -
#1669362774
exit
#1670310742
ll
#1670310743
cd public_html/
#1670310744
ll
#1670310766
find . -name "*.*" | xargs grep "이미 추천 하신 글 입니다"
#1670310807
find . -name "*.*" | xargs grep "g5_board_good"
#1670310852
pwd
#1670310853
ll
#1670311098
find . -name "*.*" | xargs grep "board_good_table"
#1670314629
ll
#1670314630
exit
#1670376945
ll
#1670376947
cd public_html/
#1670376947
ll
#1670376965
cd adm
#1670376966
ll
#1670377025
ls
#1670377029
pwd
#1670377053
cd ..
#1670377053
ll
#1670377057
cd data
#1670377057
ls
#1670377060
ll
#1670377062
cd session/
#1670377063
ll
#1670377063
ls
#1670377065
cd ..
#1670377065
ll
#1670377146
exit
#1670377392
ll
#1670377393
cd public_html/
#1670377394
ls
#1670377395
ll
#1670377397
ps -aux
#1670377398
ls
#1670377400
ll
#1670401640
exit
#1671068104
ll
#1671068105
cd public_html/
#1671068106
ll
#1671068112
ls -al
#1671068114
cd ..
#1671068117
ls -al
#1671068125
vi .bashrc 
#1671068153
vi .bash_history 
#1671068172
vi .bash_logout 
#1671068179
vi .bash_profile 
#1671412181
exit
#1672361868
ll
#1672361869
cd public_html/
#1672361869
ll
#1672361880
find . -name "*.*" | xargs grep "od_memo"
#1672362442
find . -name orderform.sub.php
#1672362787
find . -name "*.*" | xargs grep "od_status"
#1672367967
ll
#1672367969
ps -aux
#1672368982
exit
#1675061339
l
#1675061341
ll
#1675061342
cd public_html/
#1675061343
ll
#1675061349
find . -name "*.*" | xargs grep "Lfloor"
#1675061359
find . -name "*.*" | xargs grep "function Lfloor"
#1675061636
find . -name "*.*" | xargs grep "lnj.lib.php"
#1675061651
find . -name "*.*" | xargs grep "user.config.php"
#1675061663
find . -name "*.*" | xargs grep "common.lib.php"
#1675061676
find . -name "*.*" | xargs grep "user.config"
#1675061685
find . -name "*.*" | xargs grep "config"
#1675061929
exit
#1678943653
ll
#1678943668
ls -al
#1678943670
ssh-keygen -t ed25519 -C "leenamjae@nate.com"
#1678943710
eval "$(ssh-agent -s)"
#1678943744
ssh-add -k ~/.ssh/id_ed25519
#1678943761
cat ~/.ssh/id_ed25519.pub
#1678943871
git remote set-url origin leenamjae@nate.com:USERNAME/REPOSITORY.git
#1678943982
git init
#1678943982
git status
#1678943982
git add --all
#1678943983
git commit -m "End"
#1678943983
git push origin main
#1678944039
ll
#1678944040
echo "# lnj" >> README.md
#1678944042
ll
#1678944046
git init
#1678944050
git add README.md
#1678944056
git commit -m "first commit"
#1678944160
git config --global user.email "leenamjae@nate.com"
#1678944165
git config --global user.name "lnj"
#1678944171
git commit -m "first commit"
#1678944176
ll
#1678944182
git branch -M main
#1678944196
git remote add origin https://github.com/leenamjae76/lnj.git
#1678944200
git push -u origin main
#1678944311
git remote add origin git@github.com:leenamjae76/lnj.git
#1678944318
git push -u origin main
#1678944384
git remote add origin git@github.com:leenamjae76/lnj.git
#1678944384
git branch -M main
#1678944386
git push -u origin main
#1678944454
git init
#1678944454
git status
#1678944454
git add --all
#1678944454
git commit -m "End"
#1678944454
git push origin main
#1678944524
ls -al
#1678944531
cd ~/.ssh/
#1678944532
ll
#1678944849
vi config
#1678944959
ssh -T git@github.com
#1678945019
ll
#1678945023
cd ..
#1678945023
ll
#1678945047
git init
#1678945047
git status
#1678945047
git add --all
#1678945047
git commit -m "End"
#1678945047
git push origin main
#1678945610
git remote -v
#1678945689
git remote
#1678945691
git remote -v
#1678945814
ll
#1678945815
git remote -v
#1678945818
git reset
#1678945820
git remote -v
#1678945835
git reset --head ORIG_HEAD
#1678946155
git remote rm origin
#1678946158
git remote -v
#1678946219
ll
#1678946220
echo "# lnj" >> README.md
#1678946225
git init
#1678946234
git add README.md
#1678946238
git commit -m "first commit"
#1678946247
git branch -M main
#1678946254
git remote add origin git@github.com:leenamjae76/lnj.git
#1678946259
git push -u origin main
#1678946530
vi ~/.ssh/config 
#1678946881
ssh -T git@github.com
#1678948889
git config --list
#1678948952
git difftool
#1678948953
ll
#1678949302
ls -al 
#1678949304
cd .ssh
#1678949307
ls -all
#1678949315
cat known_hosts 
#1678949331
cat id_ed25519.pub 
#1678949342
clear
#1678949431
llexit
#1678949435
ll
#1678949436
cd ..
#1678949437
ll
#1678949443
cd public_html/
#1678949443
ll
#1678954426
exit
