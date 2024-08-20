import pandas as pd
from mlxtend.frequent_patterns import apriori
from mlxtend.frequent_patterns import association_rules
import json
import sys

csv_path = sys.argv[1]
print(f"Reading CSV from: {csv_path}")

df = pd.read_csv(csv_path)
print(f"DataFrame shape: {df.shape}")

# Giới hạn số lượng sản phẩm (ví dụ: 100 sản phẩm phổ biến nhất)
top_products = df['product_id'].value_counts().nlargest(200).index
df = df[df['product_id'].isin(top_products)]

# Tạo basket
basket = (df.groupby(['user_id', 'product_id'])['action']
          .sum().unstack().reset_index().fillna(0)
          .set_index('user_id'))

# Chuyển đổi thành boolean
basket_bool = basket.astype(bool)

print("Basket shape:", basket_bool.shape)

# Áp dụng thuật toán Apriori với min_support cao hơn
frequent_itemsets = apriori(basket_bool, min_support=0.001, use_colnames=True)
print("Frequent itemsets shape:", frequent_itemsets.shape)

# Tạo association rules
rules = association_rules(frequent_itemsets, metric="lift", min_threshold=1)
print("Rules shape:", rules.shape)

# Chuyển đổi kết quả thành JSON
rules_json = rules.to_json(orient='records')
print(f"Number of rules generated: {len(json.loads(rules_json))}")

# In kết quả ra stdout
print(rules_json)
