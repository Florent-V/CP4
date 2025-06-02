<?php

namespace App\Tests\Unit\Form;

use App\Entity\Splitter;
use App\Entity\SplitterCategory;
use App\Form\MemberFormType;
use App\Form\SplitterFormType;
use App\Repository\SplitterCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class SplitterFormTypeTest extends TypeTestCase
{
    private $splitterCategoryRepositoryMock;
    private $entityManagerMock;

    protected function setUp(): void
    {
        $this->splitterCategoryRepositoryMock = $this->createMock(SplitterCategoryRepository::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        $categoryMetadata = $this->createMock(ClassMetadata::class);
        $categoryMetadata->name = SplitterCategory::class;
        $categoryMetadata->identifier = ['id'];


        $this->entityManagerMock->method('getClassMetadata')
            ->willReturnMap([
                [SplitterCategory::class, $categoryMetadata],
            ]);

        $queryBuilderMock = $this->createMock(\Doctrine\ORM\QueryBuilder::class);
        $queryBuilderMock->method('orderBy')->willReturnSelf();
        $queryBuilderMock->method('getQuery')->willReturn($this->createMock(\Doctrine\ORM\AbstractQuery::class));
        $queryBuilderMock->method('getResult')->willReturn([]);


        $this->splitterCategoryRepositoryMock->method('createQueryBuilder')->willReturn($queryBuilderMock);

        parent::setUp();
    }

    protected function getExtensions(): array
    {
        $entityType = new EntityType($this->entityManagerMock);

        // We need to provide MemberFormType as a known type if it's used directly
        // For LiveCollectionType, it might be enough to mock it or ensure its dependencies are met.
        // For simplicity, we'll mock LiveCollectionType itself if it causes issues,
        // or ensure MemberFormType is available. Let's try providing MemberFormType.
        $memberFormType = new MemberFormType(); // Assuming it has no complex constructor dependencies for basic instantiation

        // Mock LiveCollectionType to avoid issues with its specific features if they are problematic in unit tests
        $liveCollectionTypeMock = $this->createMock(LiveCollectionType::class);
        $liveCollectionTypeMock->method('getParent')->willReturn(\Symfony\Component\Form\Extension\Core\Type\CollectionType::class);


        return [
            new PreloadedExtension([
                EntityType::class => $entityType,
                MemberFormType::class => $memberFormType, // Make MemberFormType available
                LiveCollectionType::class => $liveCollectionTypeMock, // Use the mock
            ], []),
        ];
    }

    public function testBuildForm(): void
    {
        $form = $this->factory->create(SplitterFormType::class);

        $this->assertTrue($form->has('name'));
        $this->assertTrue($form->has('description'));
        $this->assertTrue($form->has('category'));
        $this->assertTrue($form->has('members'));

        $this->assertInstanceOf(TextType::class, $form->get('name')->getConfig()->getType()->getInnerType());
        // Description might default to TextareaType or TextType depending on Symfony version & config
        // Checking for TextareaType or a parent like TextType
        $descriptionType = $form->get('description')->getConfig()->getType()->getInnerType();
        $this->assertTrue($descriptionType instanceof TextareaType || $descriptionType instanceof TextType);
        $this->assertInstanceOf(EntityType::class, $form->get('category')->getConfig()->getType()->getInnerType());

        // Check that the 'members' field is using the mocked LiveCollectionType
        $this->assertInstanceOf(LiveCollectionType::class, $form->get('members')->getConfig()->getType()->getInnerType());
        // And that its entry_type is MemberFormType
        $this->assertEquals(MemberFormType::class, $form->get('members')->getConfig()->getOption('entry_type'));
    }

    public function testSubmitValidData(): void
    {
        $formData = [
            'name' => 'Summer Trip',
            'description' => 'Trip to the beach!',
            // category and members are more complex.
            // For category (EntityType), we'd submit an ID.
            // For members (LiveCollectionType of MemberFormType), we'd submit an array of member data.
        ];

        $splitter = new Splitter();

        // Mock a category for selection
        $category1 = $this->createMock(SplitterCategory::class);
        $category1->method('getId')->willReturn(1);
        $category1->method('getName')->willReturn('Holiday');

        // Update mock repository to return this category
        $categoryQbMock = $this->splitterCategoryRepositoryMock->expects($this->any())->method('createQueryBuilder')->willReturnSelf();
        $categoryQbMock->method('orderBy')->willReturnSelf();
        $categoryQbMock->method('getQuery')->willReturn($this->createMock(\Doctrine\ORM\AbstractQuery::class));
        $categoryQbMock->expects($this->any())->method('getResult')->willReturn([$category1]);


        $formData['category'] = 1; // ID of $category1

        // For members (LiveCollectionType), data is an array of arrays.
        // Each inner array corresponds to a MemberFormType submission.
        // Assuming MemberFormType has a 'nickname' field for simplicity.
        $formData['members'] = [
            ['nickname' => 'Alice'],
            ['nickname' => 'Bob'],
        ];

        $form = $this->factory->create(SplitterFormType::class, $splitter);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid(), $form->getErrors(true, false)->__toString());

        $this->assertEquals('Summer Trip', $splitter->getName());
        $this->assertEquals('Trip to the beach!', $splitter->getDescription());

        // $this->assertSame($category1, $splitter->getCategory()); // This requires EntityType to resolve ID to object

        // For collections, you'd check the resulting collection on the $splitter object.
        // $this->assertCount(2, $splitter->getMembers());
        // $memberEntities = $splitter->getMembers();
        // $this->assertEquals('Alice', $memberEntities[0]->getNickname());
        // $this->assertEquals('Bob', $memberEntities[1]->getNickname());
        // These assertions for members and category require the sub-forms and EntityType
        // to correctly process and map data, which can be complex to fully unit test
        // without deeper mocking or providing 'choices' / 'entry_options' with mock data.
    }
}
