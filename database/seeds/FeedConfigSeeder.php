<?php

use Illuminate\Database\Seeder;
use App\PostRule;
use App\PostCategory;
use App\PostReport;

class FeedConfigSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'General', 
                'nameFr' => 'Général', 
                'nameAr' => 'موضوع عام', 
                'nameUr' => 'General', 
                'nameIn' => 'General', 
                'orderNumber' => 1, 
                'isActive' => true, 
            ],
            [
                'name' => 'Inquiry', 
                'nameFr' => 'Enquête', 
                'nameAr' => 'استفسار', 
                'nameUr' => 'Inquiry', 
                'nameIn' => 'Inquiry', 
                'orderNumber' => 2, 
                'isActive' => true, 
            ],
            [
                'name' => 'Recommendation', 
                'nameFr' => 'Recommandation', 
                'nameAr' => 'توصية', 
                'nameUr' => 'Recommendation', 
                'nameIn' => 'Recommendation', 
                'orderNumber' => 3, 
                'isActive' => true, 
            ],
            [
                'name' => 'Buy, sell, rent', 
                'nameFr' => 'Acheter, vendre, louer', 
                'nameAr' => 'شراء، بيع، إيجار', 
                'nameUr' => 'Buy, sell, rent', 
                'nameIn' => 'Buy, sell, rent', 
                'orderNumber' => 4, 
                'isActive' => true, 
            ],
            [
                'name' => 'Lost and found', 
                'nameFr' => 'Perdu et trouvé', 
                'nameAr' => 'مفقودات', 
                'nameUr' => 'Lost and found', 
                'nameIn' => 'Lost and found', 
                'orderNumber' => 5, 
                'isActive' => true, 
            ],
            [
                'name' => 'Meet-ups', 
                'nameFr' => 'Rencontres', 
                'nameAr' => 'لقاءات', 
                'nameUr' => 'Meet-ups', 
                'nameIn' => 'Meet-ups', 
                'orderNumber' => 6, 
                'isActive' => true, 
            ],
            [
                'name' => 'Events', 
                'nameFr' => 'Événements', 
                'nameAr' => 'أحداث', 
                'nameUr' => 'Events', 
                'nameIn' => 'Events', 
                'orderNumber' => 7, 
                'isActive' => true, 
            ],
        ];
        $rules = [
            [
                'name' => 'Respect your neighbours', 
                'nameFr' => 'Respectez vos voisins', 
                'nameAr' => 'الرجاء احترام جيرانك', 
                'nameUr' => 'Respect your neighbours', 
                'nameIn' => 'Respect your neighbours', 
                'orderNumber' => 1, 
                'isActive' => true, 
            ],
            [
                'name' => 'Do not promote or spam', 
                'nameFr' => 'Ne pas promouvoir ni spammer', 
                'nameAr' => 'لا تقوم بالترويج', 
                'nameUr' => 'Do not promote or spam', 
                'nameIn' => 'Do not promote or spam', 
                'orderNumber' => 2, 
                'isActive' => true, 
            ],
            [
                'name' => 'Only community - relevant content', 
                'nameFr' => 'Seul le contenu pertinent de la communauté', 
                'nameAr' => 'الاكتفاء فقط بالمحتوى ذي الصلة بالمجتمع', 
                'nameUr' => 'Only community - relevant content', 
                'nameIn' => 'Only community - relevant content', 
                'orderNumber' => 3, 
                'isActive' => true, 
            ],
            [
                'name' => 'No inappropriate topics', 
                'nameFr' => 'Pas de sujets inappropriés', 
                'nameAr' => 'لا مواضيع غير لائقة', 
                'nameUr' => 'No inappropriate topics', 
                'nameIn' => 'No inappropriate topics', 
                'orderNumber' => 4, 
                'isActive' => true, 
            ],
            [
                'name' => 'Abide bu UAE laws', 
                'nameFr' => 'Demeurer par les lois sur les EAU', 
                'nameAr' => 'الالتزام بقوانين الإمارات العربية المتحدة', 
                'nameUr' => 'Abide bu UAE laws', 
                'nameIn' => 'Abide bu UAE laws', 
                'orderNumber' => 5, 
                'isActive' => true, 
            ],
            [
                'name' => 'Use your real identity', 
                'nameFr' => 'Utilisez votre véritable identité', 
                'nameAr' => 'استخدم هويتك الحقيقية', 
                'nameUr' => 'Use your real identity', 
                'nameIn' => 'Use your real identity', 
                'orderNumber' => 6, 
                'isActive' => true, 
            ],
        ];
        $reports = [
            [
                'name' => 'I just don\'t like', 
                'nameFr' => 'Je n\'aime pas ça', 
                'nameAr' => 'لم يعجبني المنشور', 
                'nameUr' => 'I just don\'t like', 
                'nameIn' => 'I just don\'t like', 
                'orderNumber' => 1, 
                'isActive' => true, 
            ],
            [
                'name' => 'Nudity or sexual activity', 
                'nameFr' => 'Nudité ou activité sexuelle', 
                'nameAr' => 'عُري أو نشاط جنسي', 
                'nameUr' => 'Nudity or sexual activity', 
                'nameIn' => 'Nudity or sexual activity', 
                'orderNumber' => 2, 
                'isActive' => true, 
            ],
            [
                'name' => 'Hate speech or symbols', 
                'nameFr' => 'Discours ou symboles de haine', 
                'nameAr' => 'خطاب يحض على الكراهية', 
                'nameUr' => 'Hate speech or symbols', 
                'nameIn' => 'Hate speech or symbols', 
                'orderNumber' => 3, 
                'isActive' => true, 
            ],
            [
                'name' => 'Violence or dangerous organizations ', 
                'nameFr' => 'Violence ou organisations dangereuses', 
                'nameAr' => 'عنف أو منظمات خطرة', 
                'nameUr' => 'Violence or dangerous organizations ', 
                'nameIn' => 'Violence or dangerous organizations ', 
                'orderNumber' => 4, 
                'isActive' => true, 
            ],
            [
                'name' => 'Bullying or harassment', 
                'nameFr' => 'Intimidation ou harcèlement', 
                'nameAr' => 'اساءة أو مضايقة', 
                'nameUr' => 'Bullying or harassment', 
                'nameIn' => 'Bullying or harassment', 
                'orderNumber' => 5, 
                'isActive' => true, 
            ],
            [
                'name' => 'False information', 
                'nameFr' => 'Fausse information', 
                'nameAr' => 'معلومات زائفة', 
                'nameUr' => 'False information', 
                'nameIn' => 'False information', 
                'orderNumber' => 6, 
                'isActive' => true, 
            ],
            [
                'name' => 'Scam or fraud', 
                'nameFr' => 'Arnaque ou fraude', 
                'nameAr' => 'محتوى احتيالي', 
                'nameUr' => 'Scam or fraud', 
                'nameIn' => 'Scam or fraud', 
                'orderNumber' => 7, 
                'isActive' => true, 
            ],
            [
                'name' => 'Suicide or self-injury', 
                'nameFr' => 'Suicide ou auto-blessure', 
                'nameAr' => 'انتحار أو إيذاء الذات', 
                'nameUr' => 'Suicide or self-injury', 
                'nameIn' => 'Suicide or self-injury', 
                'orderNumber' => 8, 
                'isActive' => true, 
            ],
            [
                'name' => 'Sale of illegal or regulated goods', 
                'nameFr' => 'Vente de marchandises illégales ou réglementées', 
                'nameAr' => 'مبيعات غير مصرح بها', 
                'nameUr' => 'Sale of illegal or regulated goods', 
                'nameIn' => 'Sale of illegal or regulated goods', 
                'orderNumber' => 9, 
                'isActive' => true, 
            ],
            [
                'name' => 'Intellectual property violation', 
                'nameFr' => 'Violation de la propriété intellectuelle', 
                'nameAr' => 'انتهاك الملكية الفكرية', 
                'nameUr' => 'Intellectual property violation', 
                'nameIn' => 'Intellectual property violation', 
                'orderNumber' => 10, 
                'isActive' => true, 
            ],
            [
                'name' => 'Eating disorders', 
                'nameFr' => 'Troubles de l\'alimentation', 
                'nameAr' => 'اضطرابات الاكل', 
                'nameUr' => 'Eating disorders', 
                'nameIn' => 'Eating disorders', 
                'orderNumber' => 11, 
                'isActive' => true, 
            ],
            [
                'name' => 'Something else', 
                'nameFr' => 'Autre chose', 
                'nameAr' => 'شيء آخر', 
                'nameUr' => 'Something else', 
                'nameIn' => 'Something else', 
                'orderNumber' => 12, 
                'isActive' => true, 
            ],
        ];
        foreach ($categories as $category) {
            PostCategory::updateOrCreate(['name' => $category['name']], $category);
        }
        foreach ($rules as $rule) {
            PostRule::updateOrCreate(['name' => $rule['name']], $rule);
        }
        foreach ($reports as $report) {
            PostReport::updateOrCreate(['name' => $report['name']], $report);
        }
    }
}
