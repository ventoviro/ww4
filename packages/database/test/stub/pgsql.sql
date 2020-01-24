DROP SEQUENCE IF EXISTS "ww_flower_id_seq";
CREATE TABLE IF NOT EXISTS "ww_flower" (
	id serial NOT NULL,
	catid int NOT NULL DEFAULT 0,
	title varchar(255) NOT NULL DEFAULT '',
	meaning text DEFAULT '',
	ordering int DEFAULT 0,
	state int DEFAULT 0,
	params text DEFAULT '',
	PRIMARY KEY (id)
);

INSERT INTO "ww_flower" (id, catid, title, meaning, ordering, state, params) VALUES
	(1, 2, 'Alstroemeria', 'aspiring', 1, 0, ''),
	(2, 2, 'Amaryllis', 'dramatic', 2, 0, ''),
	(3, 1, 'Anemone', 'fragile', 3, 0, ''),
	(4, 1, 'Apple Blossom', 'promis', 4, 1, ''),
	(5, 2, 'Aster', 'contentment', 5, 1, ''),
	(6, 2, 'Azalea', 'abundance', 6, 0, ''),
	(7, 1, 'Baby''s Breath', 'festivity', 7, 1, ''),
	(8, 2, 'Bachelor Button', 'anticipation', 8, 0, ''),
	(9, 2, 'Begonia', 'deep thoughts', 9, 0, ''),
	(10, 2, 'Black-Eyed Susan', 'encouragement', 10, 0, ''),
	(11, 1, 'Camellia', 'graciousness', 11, 1, ''),
	(12, 1, 'Carnation', '', 12, 1, ''),
	(13, 1, 'pink', 'gratitude', 13, 1, ''),
	(14, 1, 'red', 'flashy', 14, 1, ''),
	(15, 1, 'striped', 'refusal', 15, 1, ''),
	(16, 1, 'white', 'remembrance', 16, 1, ''),
	(17, 1, 'yellow', 'cheerful', 17, 1, ''),
	(18, 1, 'Chrysanthemum', '', 18, 0, ''),
	(19, 2, 'bronze', 'excitement', 19, 1, ''),
	(20, 1, 'white', 'truth', 20, 0, ''),
	(21, 1, 'red', 'sharing', 21, 1, ''),
	(22, 1, 'yellow', 'secret admirer', 22, 0, ''),
	(23, 1, 'Cosmos', 'peaceful', 23, 0, ''),
	(24, 1, 'Crocus', 'foresight', 24, 0, ''),
	(25, 1, 'Daffodil', 'chivalry', 25, 1, ''),
	(26, 2, 'Delphinium', 'boldness', 26, 0, ''),
	(27, 2, 'Daisy', 'innocence', 27, 0, ''),
	(28, 1, 'Freesia', 'spirited', 28, 0, ''),
	(29, 2, 'Forget-Me-Not', 'remember me forever', 29, 1, ''),
	(30, 2, 'Gardenia', 'joy', 30, 1, ''),
	(31, 2, 'Geranium', 'comfort', 31, 1, ''),
	(32, 2, 'Ginger', 'proud', 32, 1, ''),
	(33, 2, 'Gladiolus', 'strength of character', 33, 0, ''),
	(34, 1, 'Heather', 'solitude', 34, 1, ''),
	(35, 2, 'Hibiscus', 'delicate beauty', 35, 0, ''),
	(36, 1, 'Holly', 'domestic happiness', 36, 1, ''),
	(37, 1, 'Hyacinth', 'sincerity', 37, 0, ''),
	(38, 1, 'Hydrangea', 'perseverance', 38, 1, ''),
	(39, 2, 'Iris', 'inspiration', 39, 0, ''),
	(40, 1, 'Ivy', 'fidelity', 40, 0, ''),
	(41, 1, 'Jasmine', 'grace and elegance', 41, 0, ''),
	(42, 1, 'Larkspur', 'beautiful spirit', 42, 0, ''),
	(43, 1, 'Lavender', 'distrust', 43, 0, ''),
	(44, 1, 'Lilac', 'first love', 44, 1, ''),
	(45, 1, 'Lily', '', 45, 0, ''),
	(46, 1, 'Calla', 'regal', 46, 1, ''),
	(47, 1, 'Casablanca', 'celebration', 47, 1, ''),
	(48, 2, 'Day', 'enthusiasm', 48, 1, ''),
	(49, 1, 'Stargazer', 'ambition', 49, 0, ''),
	(50, 1, 'Lisianthus', 'calming', 50, 1, ''),
	(51, 2, 'Magnolia', 'dignity', 51, 0, ''),
	(52, 1, 'Marigold', 'desire for riches', 52, 0, ''),
	(53, 1, 'Nasturtium', 'patriotism', 53, 0, ''),
	(54, 2, 'Orange Blossom', 'fertility', 54, 1, ''),
	(55, 1, 'Orchid', 'delicate beauty', 55, 0, ''),
	(56, 2, 'Pansy', 'loving thoughts', 56, 0, ''),
	(57, 1, 'Passion flower', 'passion', 57, 1, ''),
	(58, 2, 'Peony', 'healing', 58, 1, ''),
	(59, 2, 'Poppy', 'consolation', 59, 0, ''),
	(60, 1, 'Queen Anne''s Lace', 'delicate femininity', 60, 0, ''),
	(61, 1, 'Ranunculus', 'radiant', 61, 1, ''),
	(62, 1, 'Rhododendron', 'beware', 62, 0, ''),
	(63, 1, 'Rose', '', 63, 1, ''),
	(64, 2, 'pink', 'admiration/appreciation', 64, 0, ''),
	(65, 2, 'red', 'passionate love', 65, 1, ''),
	(66, 1, 'red & white', 'unity', 66, 1, ''),
	(67, 1, 'white', 'purity', 67, 1, ''),
	(68, 1, 'yellow', 'friendship', 68, 1, ''),
	(69, 2, 'Snapdragon', 'presumptuous', 69, 1, ''),
	(70, 1, 'Star of Bethlehem', 'hope', 70, 1, ''),
	(71, 1, 'Stephanotis', 'good luck', 71, 1, ''),
	(72, 2, 'Statice', 'success', 72, 1, ''),
	(73, 2, 'Sunflower', 'adoration', 73, 0, ''),
	(74, 1, 'Sweetpea', 'shyness', 74, 1, ''),
	(75, 2, 'Tuberose', 'pleasure', 75, 1, ''),
	(76, 1, 'Tulip', '', 76, 1, ''),
	(77, 2, 'pink', 'caring', 77, 1, ''),
	(78, 1, 'purple', 'royalty', 78, 0, ''),
	(79, 1, 'red', 'declaration of love', 79, 1, ''),
	(80, 1, 'white', 'forgiveness', 80, 0, ''),
	(81, 1, 'yellow', 'hopelessly in love', 81, 0, ''),
	(82, 2, 'Violet', 'faithfulness', 82, 1, ''),
	(83, 2, 'Wisteria', 'steadfast', 83, 0, ''),
	(84, 1, 'Yarrow', 'good health', 84, 1, ''),
	(85, 2, 'Zinnia', 'thoughts of friends', 85, 1, '');

ALTER SEQUENCE "ww_flower_id_seq" RESTART WITH 86;
