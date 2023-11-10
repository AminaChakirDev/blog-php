<?php

namespace App\Model;

use PDO;

class ArticleManager extends AbstractManager
{
    public const TABLE = 'article';

    public function selectAllWithCategory(): array
    {
        $query = 'SELECT article.id, article.title, article.content, article.created_at, article.updated_at, category.name as category_name FROM ' . static::TABLE . ' JOIN category ON category.id = article.category_id';

        return $this->pdo->query($query)->fetchAll();
    }

    public function selectOneByIdWithCategory(int $id): array|false
    {

      // prepared request
        $statement = $this->pdo->prepare("SELECT article.id, article.title, article.content, article.created_at, article.updated_at, category.name as category_name FROM " . static::TABLE . ' JOIN category ON category.id = article.category_id WHERE article.id=:id');
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    /**
     * Insert new article in database
     */
    public function insert(array $article): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " (`title`, `content`, `created_at`, `updated_at`, `category_id`) VALUES (:title, :content, :createdAt, :updatedAt, :categoryId)");
        $statement->bindValue('title', $article['title'], PDO::PARAM_STR);
        $statement->bindValue('content', $article['content'], PDO::PARAM_STR);
        $statement->bindValue('createdAt', date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $statement->bindValue('updatedAt', date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $statement->bindValue('categoryId', $article['categoryId'], PDO::PARAM_INT);

        $statement->execute();
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update article in database
     */
    public function update(array $article): bool
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " SET `title` = :title, `content` = :content, `updated_at` = :updatedAt, `category_id` = :categoryId WHERE id=:id");
        $statement->bindValue('id', $article['id'], PDO::PARAM_INT);
        $statement->bindValue('title', $article['title'], PDO::PARAM_STR);
        $statement->bindValue('content', $article['content'], PDO::PARAM_STR);
        $statement->bindValue('updatedAt', date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $statement->bindValue('categoryId', $article['categoryId'], PDO::PARAM_INT);

        return $statement->execute();
    }
}
